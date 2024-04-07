<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;

class ControllerMaker extends FileMaker {
    public static function getControllerBy(DatabaseSchema $jsonSchema, String $modelName): void {
        $singular = Str::singular($modelName);
        $upperSingular = Str::ucfirst($singular);
        $controller = __DIR__ . '/../../../../templates/app/Http/Controllers/SampleController.php';
        $filePath = self::getFilePathOf($modelName);
        copy($controller, $filePath);
        self::sedSampleToModelNameNotSnakely($filePath, $upperSingular);
        self::sedCOLUMNSFor($jsonSchema, $modelName, '%%FIRSTCOLUMN%%', [$jsonSchema, 'getFirstColumnNameExceptId']);

    }

    protected static function getInsertStrFor(DatabaseSchema $jsonSchema) : String {
        return "";
    }

    protected static function getFilePathOf(String $modeName): String {
        $singular = Str::singular($modeName);
        $upperSingular = Str::ucfirst($singular);
        return "./app/Http/Controllers/{$upperSingular}Controller.php";
    }
}
