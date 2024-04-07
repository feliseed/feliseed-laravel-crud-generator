<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;

use phpDocumentor\Reflection\Types\Boolean;
use Illuminate\Support\Facades\Log;


class JsonMaker {
    public static function  getJsonFor(string $tableName, bool $timestamp, bool $softDeletes, array $columns): string {
        Log::error($columns);
        $tableData = [
            "name" => $tableName,
            "timestamp" => $timestamp ? "true" : "false",
            "softDeletes" => $softDeletes ? "true" : "false",
            "columns" => []
        ];

        foreach ($columns as $column) {
            $columnName =$column->getName();
            $typeName = $column->getType()->getName();
            if($columnName === "id") {
                $typeName = "id";
            }
            if($typeName === "string") {
                $length = $column->getLength();
                $typeName = "string:" . $length;
            }
            $columnData = [
                "name" => $columnName,
                "type" => $typeName,
                "nullable" => $column->getNotnull() ? "false" : "true"
            ];
            $tableData["columns"][] = $columnData;
        }
        $jsonData = json_encode($tableData, JSON_PRETTY_PRINT);
        return $jsonData;
    }
}
