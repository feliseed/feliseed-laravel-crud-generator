<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;

class UpdateRequestMaker extends RequestMaker {
    public static function getUpdateRequestBy(DatabaseSchema $jsonSchema, String $modelName): void {
        $request = __DIR__ . '/../../../../templates/app/Http/Requests/SampleUpdateRequest.php';
        $newRequest = self::getFilePathOf($modelName);
        copy($request, $newRequest);
        $singular = Str::singular($modelName);
        $upperSingular = Str::ucfirst($singular);
        self::sedSampleToModelNameNotSnakely($newRequest, $upperSingular);
        self::sedCOLUMNSFor($jsonSchema, $modelName, '%%FIELDS_VALIDATION%%', [self::class, 'getInsertStrFor']);
    }

    protected static function getFilePathOf(String $modeName): String {
        $singular = Str::singular($modeName);
        $upperSingular = Str::ucfirst($singular);
        return "./app/Http/Requests/{$upperSingular}UpdateRequest.php";
    }
}
