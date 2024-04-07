<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;

class ModelMaker extends FileMaker {
    public static function getModelBy(DatabaseSchema $jsonSchema, String  $modelName): void {
        $model = __DIR__ . '/../../../../templates/app/Models/Sample.php';
        $filePath = self::getFilePathOf($modelName);
        copy($model, $filePath);
        $singular = Str::singular($modelName);
        $upperSingular = Str::ucfirst($singular);
        self::sedSampleToModelNameNotSnakely($filePath, $upperSingular);
        self::sedCOLUMNSFor($jsonSchema, $modelName, '%%COLUMNS%%', [self::class, 'getInsertStrFor']);
        self::sedCOLUMNSFor($jsonSchema, $modelName, '%%TIMESTAMPS%%', [self::class, 'getStrForTimeStampsBy']);
        self::sedCOLUMNSFor($jsonSchema, $modelName, '%%USE_SOFTDELETES1%%', [self::class, 'getStrForUseSoftDeletesBy']);
        self::sedCOLUMNSFor($jsonSchema, $modelName, '%%USE_SOFTDELETES2%%', [self::class, 'getStrForSoftDeletesBy']);
        self::sedCOLUMNSFor($jsonSchema, $modelName, '%%TABLE_NAME%%', [self::class, 'getStrForTableNameBy']);
    }

    protected static function getInsertStrOf(Column $column) : String {
        return '\'' . $column->name . '\'' . ",\n\t\t";
    }

    protected static function getInsertStrFor(DatabaseSchema $jsonSchema) : String {
        $tmp = array_map([self::class, 'getInsertStrOf'], $jsonSchema->columns);
        $result = str_replace(" ", "", rtrim(implode(' ', $tmp), ",\n\t\t"));
        return $result;
    }

    protected static function getFilePathOf(String $modelName): String {
        $singular = Str::singular($modelName);
        $upperSingular = Str::ucfirst($singular);
        return "./app/Models/{$upperSingular}.php";
    }

    protected static function getStrForTimeStampsBy(DatabaseSchema $jsonSchema): String {
        if($jsonSchema->hasTimeStamps) {
            return '';
        } else {
            return 'public $timestamps = false;';
        }
    }

    protected static function getStrForSoftDeletesBy(DatabaseSchema $jsonSchema): String {
        if($jsonSchema->hasSoftDeletes) {
            return 'use SoftDeletes;';
        } else {
            return '';
        }
    }

    protected static function getStrForTableNameBy(DatabaseSchema $jsonSchema): String {
        return '\'' . Str::snake(Str::plural($jsonSchema->tableName)) . '\';';
    }

    protected static function getStrForUseSoftDeletesBy(DatabaseSchema $jsonSchema): String {
        if($jsonSchema->hasSoftDeletes) {
            return 'use Illuminate\Database\Eloquent\SoftDeletes;';
        } else {
            return '';
        }
    }
}
