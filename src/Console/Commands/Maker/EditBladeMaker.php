<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;
use Illuminate\Support\Facades\Log;

class EditBladeMaker extends BladeMaker {
    public static function getEditBladeBy(DatabaseSchema $jsonSchema, String $modelName): void {
        $editBlade = __DIR__ . '/../../../../templates/resources/views/sample/edit.blade.php';
        $editBladeFilePath = self::getFilePathOf($modelName);
        copy($editBlade, $editBladeFilePath);
        self::sedSampleToModelNameNotSnakely($editBladeFilePath, Str::ucfirst(Str::singular($modelName)));
        self::sedCOLUMNSForView($jsonSchema, Str::lcfirst(Str::singular($modelName)), '%%COLUMNS%%', [self::class, 'getInsertStrFor']);
    }

    protected static function getInsertStrFor(DatabaseSchema $jsonSchema, String $modelName) : String {
        $func = function (Column $column) use ($modelName) {
            return self::getInsertStrOf($modelName, $column);
        };
        $tmp = array_map($func, $jsonSchema->columns);
        return rtrim(implode('', $tmp), ",\n\t");
    }

    protected static function getInsertStrOf(String $modelName, Column $column) : String {
        if($column->type === 'id') {
            return "";
        }
        $result = "<div>";
        $result .= "\n\t\t\t\t" . self::getLabel($column->name, !$column->nullable);
        if($column->type === "boolean") {
            $result .= "\n\t\t\t\t" . self::getCheckbox($modelName, $column->name, false);
        } else if($column->type === "text"){
            $result .= "\n\t\t\t\t" . self::getTextarea($modelName, $column->name, !$column->nullable, false);
        } else if($column->type === "time"/**H:Iの形式にformat修正 */){
            $result .= "\n\t\t\t\t" . self::getTimeInput($modelName, $column->name, !$column->nullable, false);
        } else {
            $result .= "\n\t\t\t\t" . self::getTextInput($modelName, $column->name, !$column->nullable, false);
        }
        $result .= "\n\t\t\t\t" . self::getValidationErrorMessage($column->name);
        $result .= "\n\t\t\t</div>\n\t\t\t\t\t\t";
        return $result;
    }

    protected static function getFilePathOf(String $tableName): String {
        $plural = Str::plural($tableName);
        $chainPlural = Str::snake($plural, '-');
        return "./resources/views/{$chainPlural}/edit.blade.php";
    }


}
