<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;

use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;

class IndexBladeMaker {
    public function generate(DatabaseSchema $jsonSchema, String $modelName): void {
        
        $indexBlade = file_get_contents(__DIR__ . '/../../../../stubs/index.blade.stub');
        
        // 文字列を置換
        $indexBlade = str_replace('%%ROUTE_NAME%%', Str::kebab(Str::plural($modelName)), $indexBlade);
        $indexBlade = str_replace('%%PAGE_TITLE%%', Str::ucfirst(Str::plural($modelName)), $indexBlade);
        $indexBlade = str_replace(
            '%%SEARCH_ITEM%%', 
            Str::lower(Str::singular(
                collect($jsonSchema->columns)->first(function ($column) {
                    return $column->name !== 'id';
                })->name
            )), 
            $indexBlade
        );
        $indexBlade = str_replace('%%VARIABLE_PLURAL%%', "$".Str::camel(Str::plural($modelName)), $indexBlade);
        $indexBlade = str_replace('%%VARIABLE_SINGULAR%%', "$".Str::camel(Str::singular($modelName)), $indexBlade);
        $indexBlade = str_replace('%%TABLE_COLUMNS%%', $this->getTableColumns($jsonSchema), $indexBlade);
        $indexBlade = str_replace('%%TABLE_ROWS%%', $this->getTableRows($jsonSchema, $modelName), $indexBlade);

        // create directory if not exists
        $dir = "resources/views/". Str::kebab(Str::plural($modelName));
        if (!file_exists($dir)) {
            mkdir($dir);
        }

        // publish
        file_put_contents(
            $dir ."/index.blade.php",
            $indexBlade
        );
    }

    protected function getTableColumns(DatabaseSchema $jsonSchema): String {
        $columns = $jsonSchema->columns;
        $tableColumns = '';
        foreach ($columns as $column) {
            $columnName = Str::camel($column->name);
            $tableColumns .= "<th scope=\"col\" class=\"py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6\">{$columnName}</th>\n\t\t\t\t\t\t\t\t";
        }
        return rtrim($tableColumns, ",\n\t");
    }

    protected function getTableRows(DatabaseSchema $jsonSchema, string $modelName): String {
        $columns = $jsonSchema->columns;
        $model = Str::camel(Str::singular($modelName));
        $tableRows = '';
        foreach ($columns as $column) {
            $tableRows .= "<td class=\"whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6\">\n\t\t\t\t\t\t\t\t\t<div class=\"text-gray-900\">{{\${$model}->{$column->name}}}</div>\n\t\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t\t\t";
        }
        return rtrim($tableRows, ",\n\t");
    }
}
