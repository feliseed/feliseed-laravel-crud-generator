<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands;
use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Exception;
use PDOException;


class DBConnect
{
    public static function getSchmaManager(): AbstractSchemaManager {
        try {
            $connection = DB::connection();
            $schemaManager = $connection->getDoctrineSchemaManager();
            return $schemaManager;
        } catch (PDOException $e){
            throw new PDOException("データベース接続に失敗しました ×_×　「.env」ファイルの設定を確認して下さい。");
        }
    }
}

class DBConnectFailureException extends Exception {}

