<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;

use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;

class ControllerMaker {

    public function generate(DatabaseSchema $jsonSchema, String $modelName): void {
        
        $controller = file_get_contents(__DIR__ . '/../../../../stubs/controller.stub');
        
        // 文字列を置換
        $controller = str_replace('%%ROUTE_NAME%%', Str::kebab(Str::plural($modelName)), $controller);
        $controller = str_replace('%%VIEW_NAME%%', Str::kebab(Str::plural($modelName)), $controller);
        $controller = str_replace('%%MODEL_NAME%%', Str::ucfirst(Str::camel(Str::singular($modelName))), $controller);
        $controller = str_replace(
            '%%SEARCH_ITEM%%', 
            Str::lower(Str::singular(
                collect($jsonSchema->columns)->first(function ($column) {
                    return $column->name !== 'id';
                })->name
            )), 
            $controller
        );
        $controller = str_replace('%%VARIABLE_PLURAL%%', Str::camel(Str::plural($modelName)), $controller);
        $controller = str_replace('%%VARIABLE_SINGULAR%%', Str::camel(Str::singular($modelName)), $controller);
        
        // publish
        file_put_contents(
            "app/Http/Controllers/". Str::ucfirst(Str::camel(Str::singular($modelName))) ."Controller.php",
            $controller
        );
    }
}
