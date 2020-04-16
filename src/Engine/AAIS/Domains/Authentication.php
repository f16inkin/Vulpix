<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains;


use Vulpix\Engine\Database\Connectors\IConnector;

/**
 * Аутентификация - процесс проверки учетных данных пользователя.
 *
 * Class Authentication
 * @package Vulpix\Engine\AAIS\Domains
 */
class Authentication
{
    private $_dbConnector;

    public function __construct(IConnector $dbConnector)
    {
        $this->_dbConnector = $dbConnector;
    }

    /**
     * Проверяет наличие учетной записи пользователя, для дальнейшей авторизации
     *
     * @param string $login
     * @return bool|mixed
     */
    private function findUser(string $userName){
        $query = ("SELECT `id`, `password_hash` FROM `user_accounts` WHERE `user_name` = :userName");
        $result = $this->_dbConnector::getConnection()->prepare($query);
        $result->execute([
            'userName' => $userName
        ]);
        if ($result->rowCount() > 0){
            return $accountData = $result->fetch();
        }
        return false;
    }

    public function authenticate(string $userName, string $userPassword){
        $userData = $this->findUser($userName);
        if ($userData !== false){
            $hash = $userData['password_hash'];
            $id = $userData['id'];
            if (password_verify($userPassword, $hash)){
                /**
                 * Дальше мне нужно каждый установить уникальный ключ, который будет потом сверятс яс сессией и
                 * Cookie для того, чтобы знать. Был ли выполнен вход в эту учетную запись на другой машине.
                 * При каждом новом логине ключ обновляется, а значит, на старом устройстве будет log off.
                 * --------------------------------------------------------------------------------------------
                 * Если пароль прошел, то обновляю ключ
                 */
                $key = sha1(uniqid().$userName);
                $query = ("UPDATE `user_accounts` SET `secret_key` = :secret_key WHERE `id` = :id");
                $result = $this->_dbConnector::getConnection()->prepare($query);
                $result->execute([
                    'secret_key' => $key,
                    'id' => $id
                ]);
                return $token = JWTCreator::create();
            }
        }
    }

}