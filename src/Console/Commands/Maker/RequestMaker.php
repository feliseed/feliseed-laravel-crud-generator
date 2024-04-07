<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;
use InvalidArgumentException;

abstract class RequestMaker extends FileMaker {

    protected static function getInsertStrFor(DatabaseSchema $jsonSchema) : String {
        $tmp = array_map([self::class, 'getInsertStrOf'], $jsonSchema->columns);
        return rtrim(implode('', $tmp), ",\n\t\t");
    }

    protected static function getInsertStrOf(Column $column) : String {
        $required = $column->nullable ? "nullable" : "required";
        if ($column->type === "boolean") {
            $required = "nullable";
        }

        if($column->name === 'id') {
            return "";
        }
        else {
            return "'{$column->name}' => ['" . $required . "'," . self::getStrFor($column->type) . "],\n\t\t\t";
        }
    }


    //これメソッド名どうにかならないのか
    private static function getStrFor(String $type) : String {
        if( /**「string:数値」の形式かどうかを判定 */
            strpos($type, "string:") === 0 &&
            count(explode(":", $type)) == 2 &&
            is_numeric(explode(":", $type)[1])
        ) {
            $length = explode(":", $type)[1];
            return "'string', 'max:" . $length . "'";
        } else if($type === "integer") {
            return "'integer'";
        } else if($type === "boolean") {
            return "'boolean'";
        } else if($type === "text") {
            return "'string'";
        } else if($type === "date") {
            return "'date'";
        } else if($type === "time") {
            return "'date_format:H:i'";
        } else if($type === "datetime") {
            return "'date'";
        } else {
            throw new InvalidArgumentException("Invalid column type");
        }
    }

    //これメソッド名どうにかならないのか
    private static function getStrOf(bool $nullable) : String {
        if($nullable) {
            return "'nullable', ";
        } else {
            return "'required', ";
        }
    }

    protected static function getStoreRequestFilePathOf(String $tableName): String {
        $singular = Str::singular($tableName);
        $upperSingular = Str::ucfirst($singular);
        return "./app/Http/Requests/{$upperSingular}UpdateRequest.php";
    }

}
