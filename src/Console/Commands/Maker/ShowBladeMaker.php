<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;

class ShowBladeMaker extends FileMaker {
    public static function getShowBladeBy(DatabaseSchema $jsonSchema, String $modelName): void {
        $singular = Str::singular($modelName);
        $upperSingular = Str::ucfirst($singular);
        $plural = Str::plural($modelName);
        $chainPlural = Str::snake($plural, '-');
        $showblade = __DIR__ . '/../../../../templates/resources/views/sample/show.blade.php';
        $newView = "./resources/views/{$chainPlural}/show.blade.php";
        copy($showblade, $newView);
        self::sedSampleToModelNameNotSnakely($newView, $upperSingular);
    }
    //overrideしないといけなくなってしまった。
    protected static function getInsertStrOf(Column $column) : String {
        return "";
    }


    protected static function getInsertStrFor(DatabaseSchema $jsonSchema) : String {
        return "";
    }
    //overrideしないといけなくなってしまった。
    protected static function getFilePathOf(String $tableName): String {
        return "";
    }
}
