<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;
use PhpParser\Node\Expr\Cast\String_;

class IndexBladeMaker extends FileMaker {
    public static function getIndexBladeBy(DatabaseSchema $jsonSchema, String $modelName): void {
        $singular = Str::singular($modelName);
        $upperSingular = Str::ucfirst($singular);
        $indexBlade = __DIR__ . '/../../../../templates/resources/views/sample/index.blade.php';
        $indexBladeFilePath = self::getFilePathOf($modelName);
        copy($indexBlade, $indexBladeFilePath);
        self::sedSampleToModelNameNotSnakely($indexBladeFilePath, $upperSingular);
        self::sedCOLUMNSFor($jsonSchema, $modelName, '%%HEADCOLUMNS%%', [self::class, 'getHEADInsertStrFor']);
        self::sedCOLUMNSFor($jsonSchema, $modelName, '%%FIRSTCOLUMN%%', [$jsonSchema, 'getFirstColumnNameExceptId']);

        self::sedCOLUMNSForView($jsonSchema, Str::lcfirst(Str::singular($modelName)), '%%BODYCOLUMNS%%', [self::class, 'getBODYInsertStrFor']);

    }

    protected static function getHEADInsertStrOf(Column $column) : String {
        return "<th scope=\"col\" class=\"py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6\">{$column->name}</th>\n\t\t\t\t\t\t\t\t";
    }

    protected static function getHEADInsertStrFor(DatabaseSchema $jsonSchema) : String {
        $tmp = array_map([self::class, 'getHEADInsertStrOf'], $jsonSchema->columns);
        return rtrim(implode('', $tmp), ",\n\t");
    }

    protected static function getBODYInsertStrOf(String $name, Column $column) : String {
        return "<td class=\"whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6\">\n\t\t\t\t\t\t\t\t\t<div class=\"text-gray-900\">{{\$$name->$column->name}}</div>\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t";
    }

    protected static function getBODYInsertStrFor(DatabaseSchema $jsonSchema, String $modelName) : String {
        $func = function (Column $column) use ($modelName) {
            return self::getBODYInsertStrOf($modelName, $column);
        };
        $tmp = array_map($func, $jsonSchema->columns);
        return rtrim(implode('', $tmp), ",\n\t");
    }

    // protected static function getFirstColumnOf(DatabaseSchema $jsonSchema) : String {
        // return "<div>
        // \t\t\t<x-atoms.label for=\"$columnName\" :value=\"__('$columnName')\"/>
        // \t\t\t<x-atoms.input id=\"$columnName\" class=\"mt-1 w-44\" type=\"text\" name=\"$columnName\" :value=\"request()->get('$columnName')\" autocomplete=\"off\" />
        // \t\t</div>";
    // }

    protected static function getFilePathOf(String $tableName): String {
        $plural = Str::plural($tableName);
        $chainPlural = Str::snake($plural, '-');
        return "./resources/views/{$chainPlural}/index.blade.php";
    }

}
