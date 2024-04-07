<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;
use InvalidArgumentException;

class ControllerTestMaker extends FileMaker {
    public static function getControllerTestBy(DatabaseSchema $jsonSchema, String $modelName): void {
        $singular = Str::singular($modelName);
        $upperSingular = Str::ucfirst($singular);
        $templateFilePath = __DIR__ . '/../../../../templates/tests/Feature/Http/Controllers/SampleControllerTest.php';
        $filePath = self::getFilePathOf($modelName);
        copy($templateFilePath, $filePath);
        self::sedSampleToModelNameNotSnakely($filePath, $upperSingular);
        self::sedCOLUMNSForView($jsonSchema, Str::lcfirst($singular), '%%ASSERT_COLUMNS%%', [self::class, 'getInssertAssertStrFor']);
        self::sedCOLUMNSFor($jsonSchema, $modelName, '%%COLUMNS%%', [self::class, 'getInsertStrFor']);

    }

    protected static function getInsertStrOf(Column $column) : String {
        if($column->type == "id") {
            return "";
        } else if(
            /**「string:数値」の形式かどうかを判定 */
            strpos($column->type, "string:") === 0 &&
            count(explode(":", $column->type)) == 2 &&
            is_numeric(explode(":", $column->type)[1])
        ) {
            $length = explode(":", $column->type)[1];
            return "'{$column->name}' => \$this->faker->lexify(str_repeat('?', " . $length . ")),\n\t\t\t";        } else if($column->type == "integer") {
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

    //TODO:dry化
    protected static function getInsertStrFor(DatabaseSchema $jsonSchema) : String {
        $tmp = array_map([self::class, 'getInsertStrOf'], $jsonSchema->columns);
        return rtrim(implode('', $tmp), ",\n\t");
    }

    protected static function getInssertAssertStrFor(DatabaseSchema $jsonSchema, String $modelName) : String {
        $func = function($column) use ($modelName) {
            return self::getInsertAssertStrOf($modelName, $column);
        };
        $tmp = array_map($func, $jsonSchema->columns);
        return rtrim(implode('', $tmp), ",\n\t");
    }

    private static function getInsertAssertStrOf(String $modelName, Column $column) : String {
        return "'{$column->name}' => $$modelName->{$column->name},\n\t\t\t";
    }

    protected static function getFilePathOf(String $modeName): String {
        $singular = Str::singular($modeName);
        $upperSingular = Str::ucfirst($singular);
        return "./tests/Feature/Http/Controllers/{$upperSingular}ControllerTest.php";
    }
}
