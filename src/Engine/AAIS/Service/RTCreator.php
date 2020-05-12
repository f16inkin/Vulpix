<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Service;

use Ramsey\Uuid\Uuid;
use Vulpix\Engine\Database\Connectors\IConnector;

/**
 * Класс декоратор над Ramsey/UUid. Создает Uuid4 Refresh Token.
 *
 * Class RTCreator
 * @package Vulpix\Engine\AAIS\Domains
 */
class RTCreator
{
    private $_dbConnector;

    /**
     * RTCreator constructor.
     * @param IConnector $dbConnector
     */
    public function __construct(IConnector $dbConnector)
    {
        $this->_dbConnector = $dbConnector;
    }

    /**
     * Установит значение нового сгенерированного токена в БД.
     *
     * @param string $token
     * @param array $accountDetails
     * @return bool
     */
    private function insertToken(string $token, array $accountDetails) : void
    {
        $query = ("INSERT INTO `refresh_tokens` (id, token, user_id, created, expires) VALUES (null, :token, :userId, :created, :expires )");
        $result = $this->_dbConnector::getConnection()->prepare($query);
        $result->execute([
            'token' => $token,
            'userId' => $accountDetails['userId'],
            'created' => time(),
            'expires' => time() + 60*60*24*30
        ]);
    }

    /**
     * Удалит старый рефреш токен для текущего пользователя.
     *
     * @param array $accountDetails
     * @return bool
     */
    private function deleteToken(array $accountDetails) : void
    {
        $query = ("DELETE FROM `refresh_tokens` WHERE `user_id` = :userId");
        $result = $this->_dbConnector::getConnection()->prepare($query);
        $result->execute([
            'userId' => $accountDetails['userId']
        ]);
    }

    /**
     * Метод создает новый refresh token.
     *
     * @param array $accountDetails
     * @return string
     */
    public function create(array $accountDetails) : string
    {
        $this->deleteToken($accountDetails);
        $token = (Uuid::uuid4())->toString();
        $this->insertToken($token, $accountDetails);
        return $token;
    }

}