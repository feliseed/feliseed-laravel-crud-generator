<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;

use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;


class SeederMaker {

    public function getSeederBy(String $modelName): void {
        
        $model = file_get_contents(__DIR__ . '/../../../../stubs/seeder.stub');
        
        // 文字列を置換
        $model = str_replace('%%MODEL_NAME%%', Str::ucfirst(Str::camel(Str::singular($modelName))), $model);

        // publish
        file_put_contents(
            "database/seeders/". Str::ucfirst(Str::camel(Str::singular($modelName))) ."Seeder.php",
            $model
        );
    }
}
