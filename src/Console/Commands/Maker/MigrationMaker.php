<?php
namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;
use InvalidArgumentException;
use Illuminate\Support\Facades\Log;

class MigrationMaker extends FileMaker {
    public static function getMigrationBy(DatabaseSchema $jsonSchema, String $modelName): void {

        $plural = Str::plural($jsonSchema->tableName);
        $chainPlural = Str::snake($plural);
        $migration = __DIR__ . '/../../../../templates/database/migrations/2022_12_01_100506_create_samples_table.php';
        $filePath = self::getFilePathOf($modelName);
        copy($migration, $filePath);
        self::sedCOLUMNSFor($jsonSchema, $modelName, '%%COLUMNS%%', [self::class, 'getInsertStrFor']);
        self::sedCOLUMN($filePath, $chainPlural, '%%MODEL_NAME%%');
        self::sedSampleToModelNameSnakely($filePath, Str::lcfirst($chainPlural));

    }

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

    protected static function getInsertStrFor(DatabaseSchema $jsonSchema) : String {
        $tmp = array_map([self::class, 'getInsertStrOf'], $jsonSchema->columns);
        if($jsonSchema->hasTimeStamps) {
            $tmp[] = "\$table->timestamps();\n\t\t\t";
        }
        if($jsonSchema->hasSoftDeletes) {
            $tmp[] = "\$table->softDeletes();";
        }
        return str_replace("\t ", "\t", rtrim(implode(' ', $tmp), ",\n\t"));
    }

    protected static function getFilePathOf(String $modeName): String {
        $singular = Str::singular($modeName);
        $plural = Str::plural($singular);
        $snakePlural = Str::snake($plural);
        $migrationFilePath = "./database/migrations/2022_12_01_100506_create_{$snakePlural}_table.php";
        return $migrationFilePath;
    }




}
