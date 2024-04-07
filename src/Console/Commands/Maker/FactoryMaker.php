<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;
use InvalidArgumentException;

class FactoryMaker extends FileMaker {
    public static function getFactoryBy(DatabaseSchema $jsonSchema, String $modelName): void {
        $singular = Str::singular($modelName);
        $upperSingular = Str::ucfirst($singular);
        $factory = __DIR__ . '/../../../../templates/database/factories/SampleFactory.php';
        $factoryFilePath = self::getFilePathOf($modelName);
        copy($factory, $factoryFilePath);
        self::sedSampleToModelNameNotSnakely($factoryFilePath, $upperSingular);
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
            return "'{$column->name}' => \$this->faker->time(\$format = 'H:i:s', \$max = 'now'),\n\t\t\t";
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

    protected static function getFilePathOf(String $tableName): String{
        $singular = Str::singular($tableName);
        $upperSingular = Str::ucfirst($singular);
        $factoryFilePath = "./database/factories/{$upperSingular}Factory.php";
        return $factoryFilePath;
    }

}
