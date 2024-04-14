<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;

use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;
use InvalidArgumentException;

class ControllerTestMaker {

    public function generate(DatabaseSchema $jsonSchema, String $modelName): void {
        
        $controller = file_get_contents(__DIR__ . '/../../../../stubs/test.stub');
        
        // 文字列を置換
        $controller = str_replace('%%ROUTE_NAME%%', Str::kebab(Str::plural($modelName)), $controller);
        $controller = str_replace('%%VIEW_NAME%%', Str::kebab(Str::plural($modelName)), $controller);
        $controller = str_replace('%%MODEL_NAME%%', Str::ucfirst(Str::camel(Str::singular($modelName))), $controller);
        $controller = str_replace('%%VARIABLE_PLURAL%%', Str::camel(Str::plural($modelName)), $controller);
        $controller = str_replace('%%VARIABLE_SINGULAR%%', Str::camel(Str::singular($modelName)), $controller);
        $controller = str_replace('%%FAKE_DATA%%', $this->getFakeData($jsonSchema), $controller);
        $controller = str_replace('%%ASSERT_COLUMNS%%', $this->getAssertColumns($jsonSchema, $modelName), $controller);

        // create directory if not exists
        $dir = "tests/Feature/Http/Controllers";
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // publish
        file_put_contents(
            "tests/Feature/Http/Controllers/". Str::ucfirst(Str::camel(Str::singular($modelName))) ."ControllerTest.php",
            $controller
        );
    }
    
    // FIXME: 可読性低い
    protected function getFakeData(DatabaseSchema $jsonSchema) : String {
        $tmp = array_map([self::class, 'getInsertStrOf'], $jsonSchema->columns);
        return rtrim(implode('', $tmp), ",\n\t");
    }
    // FIXME: 可読性低い
    protected function getInsertStrOf(Column $column) : String {
        if($column->type == "id") {
            return "";
        } else if(
            /**「string:数値」の形式かどうかを判定 */
            strpos($column->type, "string:") === 0 &&
            count(explode(":", $column->type)) == 2 &&
            is_numeric(explode(":", $column->type)[1])
        ) {
            $length = explode(":", $column->type)[1];
            return "'{$column->name}' => \$this->faker->lexify(str_repeat('?', " . $length . ")),\n\t\t\t";        
        } else if($column->type == "integer") {
            return "'{$column->name}' => \$this->faker->numberBetween(\$min = 1, \$max = 100),\n\t\t\t";
        } else if($column->type == "boolean") {
            return "'{$column->name}' => \$this->faker->boolean,\n\t\t\t";
        } else if($column->type == "text") {
            return "'{$column->name}' => \$this->faker->text,\n\t\t\t";
        } else if($column->type == "date") {
            return "'{$column->name}' => \$this->faker->date(\$format = 'Y-m-d', \$max = 'now'),\n\t\t\t";
        } else if($column->type == "time") {
            return "'{$column->name}' => \$this->faker->time(\$format = 'H:i', \$max = 'now'),\n\t\t\t";
        } else if($column->type == "datetime") {
            return "'{$column->name}' => \$this->faker->dateTimeThisDecade->format('Y-m-d H:i:s'),\n\t\t\t";
        } else {
            throw new InvalidArgumentException("Invalid column type");
        }
    }

    // FIXME: 可読性低い
    protected function getAssertColumns(DatabaseSchema $jsonSchema, String $modelName) : String {
        $func = function($column) use ($modelName) {
            $model = Str::camel(Str::singular($modelName));
            return "'{$column->name}' => \${$model}->{$column->name},\n\t\t\t";
        };
        $tmp = array_map($func, $jsonSchema->columns);
        return rtrim(implode('', $tmp), ",\n\t");
    }
}
