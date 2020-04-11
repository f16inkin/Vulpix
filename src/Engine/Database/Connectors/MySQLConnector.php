<?php

declare(strict_types = 1);

namespace Vulpix\Engine\Database\Connectors;


class MySQLConnector implements IConnector
{
    private static $_PDO = null;
    private function __construct() {}
    private function __clone() {}

    /**
     * Вернет подключение к базе данных
     * --------------------------------
     * @return \PDO
     */
    public static function getConnection()
    {
        if (is_null(self::$_PDO)) {
            try {
                $params = include 'configs/database.php';
                $host = $params['host'];
                $db = $params['dbname'];
                $charset = $params['charset'];
                $user = $params['user'];
                $password = $params['password'];
                $options = $params['options'];
                //Переменные опций PDO
                $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
                self::$_PDO = new \PDO($dsn, $user, $password, $options);
            } catch (\PDOException $e) {
                //Обработать ошибку
            }
        }
        return self::$_PDO;
    }
}