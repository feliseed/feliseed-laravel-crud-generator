<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;

use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;
use InvalidArgumentException;

class MigrationMaker {

    public function generate(DatabaseSchema $jsonSchema, String $modelName): void {
        
        $migration = file_get_contents(__DIR__ . '/../../../../stubs/migration.stub');
        
        // 文字列を置換
        $migration = str_replace('%%TABLE_NAME%%', $jsonSchema->tableName, $migration);
        $migration = str_replace('%%COLUMNS%%', $this->getColumns($jsonSchema), $migration);
        
        // publish
        file_put_contents(
            "database/migrations/". date('Y_m_d_His_') . "create_" .  $jsonSchema->tableName ."_table.php",
            $migration
        );
    }

    // FIXME: 可読性低い
    protected static function getColumns(DatabaseSchema $jsonSchema) : String {
        $tmp = array_map([self::class, 'getInsertStrOf'], $jsonSchema->columns);
        if($jsonSchema->hasTimeStamps) {
            $tmp[] = "\$table->timestamps();\n\t\t\t";
        }
        if($jsonSchema->hasSoftDeletes) {
            $tmp[] = "\$table->softDeletes();";
        }
        return str_replace("\t ", "\t", rtrim(implode(' ', $tmp), ",\n\t"));
    }
    // FIXME: 可読性低い
    protected static function getInsertStrOf(Column $column) : String {
        $result = "";
        if(
            /**「string:数値」の形式かどうかを判定 */
            strpos($column->type, "string:") === 0 &&
            count(explode(":", $column->type)) == 2 &&
            is_numeric(explode(":", $column->type)[1])
        ) {
            $length = explode(":", $column->type)[1];
            $result = $result . "\$table->string('{$column->name}', {$length})";
        } else if(in_array($column->type, [
            "id",
            "integer",
            "boolean",
            "text",
            "date",
            "time",
            "datetime"
        ])) {
            $result = $result . "\$table->{$column->type}('{$column->name}')";
        } else {
            throw new InvalidArgumentException("Invalid column type");
        }
        if($column->nullable == "true") {
            $result = $result . "->nullable()";
        }
        $result = $result . ";\n\t\t\t";
        return $result;
    }

}
