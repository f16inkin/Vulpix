<?php


namespace Vulpix\Engine\Database\Connectors;


interface IConnector
{
    /**
     * Вернет подключение к базе данных
     * --------------------------------
     * @return \PDO
     */
    public static function getConnection();

}