<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;

class ModelMaker {

    public function getModelBy(DatabaseSchema $jsonSchema, String $modelName): void {
        
        $model = file_get_contents(__DIR__ . '/../../../../stubs/model.stub');
        
        // 文字列を置換
        $model = str_replace('%%MODEL_NAME%%', Str::ucfirst(Str::camel(Str::singular($modelName))), $model);
        $model = str_replace('%%TABLE_NAME%%', Str::snake(Str::plural($jsonSchema->tableName)), $model);
        $model = str_replace('%%FILLABLE%%', $this->getFillable($jsonSchema), $model);
        $model = str_replace('%%TIMESTAMPS%%',  'public $timestamps = ' . ($jsonSchema->hasTimeStamps ? 'true;' : 'false;'), $model);
        $model = str_replace('%%USE_SOFTDELETE%%', $jsonSchema->hasSoftDeletes ? 'use Illuminate\Database\Eloquent\SoftDeletes;' : '', $model);
        $model = str_replace('%%TRAIT_SOFTDELETE%%', $jsonSchema->hasSoftDeletes ? 'use SoftDeletes;' : '', $model);
        
        // publish
        file_put_contents(
            "app/Models/". Str::ucfirst(Str::camel(Str::singular($modelName))) .".php",
            $model
        );
    }

    protected function getFillable(DatabaseSchema $jsonSchema): String {
        $tmp = array_map(function($column) {
            return "'{$column->name}'";
        }, $jsonSchema->columns);
        return implode(",\n\t\t", $tmp);
    }

}
