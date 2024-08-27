<?php

function dbConnect(array $config): PgSql\Connection|false
{
    static $db = null;

    if (is_null($db)) {
        $db = @pg_connect(getConnectionString($config));
        if (!$db) {
            echo handleError("Ошибка соединения с БД");
            exit ;
        }
    }

    return $db;
}

function getConnectionString(array $config): string
{
    return sprintf("host=%s port=%s dbname=%s user=%s password=%s",
        $config['db']['host'],
        $config['db']['port'],
        $config['db']['dbname'],
        $config['db']['user'],
        $config['db']['password']);
}