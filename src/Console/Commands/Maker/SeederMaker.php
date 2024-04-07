<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;


class SeederMaker extends FileMaker {
    public static function getSeederBy(String $modelName): void {
        $seeder = __DIR__ . '/../../../../templates/database/seeders/SampleSeeder.php';
        $newSeeder = self::getFilePathOf($modelName);
        copy($seeder, $newSeeder);
        $singular = Str::singular($modelName);
        $upperSingular = Str::ucfirst($singular);
        self::sedSampleToModelNameNotSnakely($newSeeder, $upperSingular);
    }



    protected static function getFilePathOf(String $modeName): String {
        $singular = Str::singular($modeName);
        $upperSingular = Str::ucfirst($singular);
        return  "./database/seeders/{$upperSingular}Seeder.php";
    }
}
