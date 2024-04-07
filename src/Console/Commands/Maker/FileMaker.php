<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;

abstract class FileMaker {
    protected static function sedSampleToModelNameNotSnakely(string $fileName, string $singular): void {
        $arrayReplace = array(
            'Sample' => Str::ucfirst($singular),
            'sample' => Str::camel($singular),
            'Samples' => Str::ucfirst(Str::plural($singular)),
            'samples' => Str::camel(Str::plural($singular)),
            'samplesChainCase' => Str::snake(Str::plural($singular), '-'),
        );
        $buff = file_get_contents($fileName);
        $buff = strtr($buff, $arrayReplace);
        file_put_contents($fileName, $buff);
    }

    protected static function sedSampleToModelNameSnakely(string $fileName, string $singular): void {
        $arrayReplace = array(
            'sample' => Str::snake($singular),
            'samples' => Str::snake(Str::plural($singular)),
        );
        $buff = file_get_contents($fileName);
        $buff = strtr($buff, $arrayReplace);
        file_put_contents($fileName, $buff);
    }

    public static function mkdirIfNotExists($tableName): void {
        $singular = Str::singular($tableName);
        $plural = Str::plural($singular);
        $chainPlural = Str::snake($plural, '-');
        $view_directory_path = "./resources/views/{$chainPlural}";
        if(!file_exists($view_directory_path)){
            mkdir($view_directory_path, 0744);
        }
        if(!is_dir('./tests')){
            mkdir('./tests', 0744);
        }
        if(!is_dir('./tests/Feature')){
            mkdir('./tests/Feature', 0744);
        }
        if(!is_dir('./tests/Feature/Http')){
            mkdir('./tests/Feature/Http', 0744);
        }
        if(!is_dir('./tests/Feature/Http/Controllers')){
            mkdir('./tests/Feature/Http/Controllers', 0744);
        }
        if(!is_dir('./app')){
            mkdir('./app', 0744);
        }
        if(!is_dir('./app/Models')){
            mkdir('./app/Models', 0744);
        }
        if(!is_dir('./app/Http')){
            mkdir('./app/Http', 0744);
        }
        if(!is_dir('./app/Http/Controllers')){
            mkdir('./app/Http/Controllers', 0744);
        }
        if(!is_dir('./app/Http/Requests')){
            mkdir('./app/Http/Requests', 0744);
        }
        if(!is_dir('./templates')){
            mkdir('./templates', 0744);
        }
        if(!is_dir('./database')){
            mkdir('./database', 0744);
        }
        if(!is_dir('./database/factories')){
            mkdir('./database/factories', 0744);
        }
        if(!is_dir('./database/migrations')){
            mkdir('./database/migrations', 0744);
        }
        if(!is_dir('./database/seeders')){
            mkdir('./database/seeders', 0744);
        }
        if(!is_dir('./resources')){
            mkdir('./resources', 0744);
        }
        if(!is_dir('./resources/views')){
            mkdir('./resources/views', 0744);
        }
    }

    protected static function sedCOLUMN(string $fileName, string $insertStr, string $sedTargetStr): void {
        $buff = file_get_contents($fileName);
        $buff = strtr($buff, array(
            $sedTargetStr => $insertStr,
        ));
        file_put_contents($fileName, $buff);
    }

    protected static function sedCOLUMNSFor(DatabaseSchema $jsonSchema,String $modelName, String $sedTargetStr, callable $funcToStrFrom): void {
        $modelFilePath = static::getFilePathOf($modelName);
        $insertStr = $funcToStrFrom($jsonSchema);
        self::sedCOLUMN($modelFilePath, $insertStr, $sedTargetStr);
    }

    protected static function sedCOLUMNSForView(DatabaseSchema $jsonSchema, String $modelName, String $sedTargetStr, callable $funcToStrFrom): void {
        $modelFilePath = static::getFilePathOf($modelName);
        $insertStr = $funcToStrFrom($jsonSchema, $modelName);
        self::sedCOLUMN($modelFilePath, $insertStr, $sedTargetStr);
    }

    abstract protected static function getFilePathOf(String $tableName);

}
