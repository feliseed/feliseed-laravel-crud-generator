<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;

class CreateBladeMaker extends BladeMaker {
    public static function getCreateBladeBy(DatabaseSchema $jsonSchema, String $modelName): void {
        $singular = Str::singular($modelName);
        $upperSingular = Str::ucfirst($singular);
        $createblade = __DIR__ . '/../../../../templates/resources/views/sample/create.blade.php';
        $createBladeFilePath = self::getFilePathOf($modelName);
        copy($createblade, $createBladeFilePath);
        self::sedSampleToModelNameNotSnakely($createBladeFilePath, $upperSingular);
        self::sedCOLUMNSFor($jsonSchema, $modelName, '%%COLUMNS%%', [self::class, 'getInsertStrFor']);
    }

    protected static function getInsertStrFor(DatabaseSchema $jsonSchema) : String {
        $tmp = array_map([self::class, 'getInsertStrOf'], $jsonSchema->columns);
        return rtrim(implode('', $tmp), ",\n\t");
    }

    protected static function getInsertStrOf(Column $column) : String {
        if($column->type === 'id') {
            return "";
        }
        $result = "<div>";
        $result .= "\n\t\t\t\t" . self::getLabel($column->name, !$column->nullable);
        if($column->type === "boolean") {
            $result .= "\n\t\t\t\t" . self::getCheckbox(null, $column->name);
        } else if($column->type === "text"){
            $result .= "\n\t\t\t\t" . self::getTextarea(null, $column->name, !$column->nullable);
        } else if($column->type === "time"/**H:Iの形式にformat修正 */){
            $result .= "\n\t\t\t\t" . self::getTimeInput(null, $column->name, !$column->nullable);
        } else {
            $result .= "\n\t\t\t\t" . self::getTextInput(null, $column->name, !$column->nullable);
        }
        $result .= "\n\t\t\t\t" . self::getValidationErrorMessage($column->name);
        $result .= "\n\t\t\t</div>\n\t\t\t\t\t\t";
        return $result;
    }

    protected static function getFilePathOf(String $modelName): String {
        $plural = Str::plural($modelName);
        $chainPlural = Str::snake($plural, '-');
        return "./resources/views/{$chainPlural}/create.blade.php";
    }

}
