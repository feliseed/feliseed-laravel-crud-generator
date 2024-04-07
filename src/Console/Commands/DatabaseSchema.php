<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DatabaseSchema
{
    public string $tableName;
    public bool $hasTimeStamps;
    public bool $hasSoftDeletes;
    public array $columns;

    public function __construct(string $jsonString)
    {
        $data = json_decode($jsonString);
        //singularのupper camelで入力
        $this->tableName = Str::singular(Str::camel($data->name));
        $this->hasTimeStamps = filter_var($data->timestamp, FILTER_VALIDATE_BOOLEAN);
        $this->hasSoftDeletes = filter_var($data->softDeletes, FILTER_VALIDATE_BOOLEAN);
        $this->columns = array_map(function ($column) {
            return new Column($column->name, $column->type, filter_var($column->nullable, FILTER_VALIDATE_BOOLEAN));
        }, $data->columns);
    }

    public function getColumnNames(): array
    {
        $columnNames = [];
        foreach ($this->columns as $column) {
            $columnNames[] = $column->name;
        }
        return $columnNames;
    }

    public function getFirstColumnNameExceptId(): String {
        foreach ($this->columns as $column) {
            if($column->name !== 'id') {
                return $column->name;
            }
        }
        throw new NoColumnsFoundException("id以外のカラムが見つかりません。");
    }

    //sedCOLUMNSForに渡すためにstaticメソッドを作る
    protected static function getFirstColumnOf(JsonSchema $jsonSchema) : String {
        $columnName = $jsonSchema->getFirstColumnNameExceptId();
        return $columnName;
    }

    public function isDuplicateTimeStamp(): bool {
        // タイムスタンプが true であるかどうかをチェック
        if ($this->hasTimeStamps) {
            // 'created_at' または 'updated_at' カラムが存在するかどうかをチェック
            $columnNames = $this->getColumnNames();
            if (in_array('created_at', $columnNames) || in_array('updated_at', $columnNames)) {
                return true;
            }
        }

        return false;
    }

}

class NoColumnsFoundException extends Exception {}


