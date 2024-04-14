<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;

use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;

class UpdateRequestMaker {
    
    public function generate(DatabaseSchema $jsonSchema, String $modelName): void {
        
        $controller = file_get_contents(__DIR__ . '/../../../../stubs/update-request.stub');
        
        // 文字列を置換
        $controller = str_replace('%%MODEL_NAME%%', Str::ucfirst(Str::camel(Str::singular($modelName))), $controller);
        $controller = str_replace('%%RULES%%', $this->getRules($jsonSchema), $controller);
        
        // publish
        file_put_contents(
            "app/Http/Requests/". Str::ucfirst(Str::camel(Str::singular($modelName))) ."UpdateRequest.php",
            $controller
        );
    }

    protected function getRules(DatabaseSchema $jsonSchema): String {
        $result = '';

        foreach($jsonSchema->columns as $column) {

            if($column->name === 'id') continue;

            $required = $column->nullable ? "nullable" : "required";
            $format = match(explode(":", $column->type)[0]) {
                'string' => (function() use ($column){
                    $length = explode(":", $column->type)[1];
                    $max = $length ? "|max:{$length}" : '';
                    return "string{$max}";
                })(),
                'integer' => "integer",
                'boolean' => "boolean",
                'text' => "string",
                'date' => "date",
                'time' => "date_format:H:i",
                'datetime' => "date",
            };

            $result .= "'{$column->name}' => '{$required}|{$format}',\n\t\t\t";
        }

        return $result;
    }
}
