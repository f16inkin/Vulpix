<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains;


use Laminas\Diactoros\Response\JsonResponse;
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
    private function findAccount(string $userName){
        $query = ("SELECT `id`, `user_name`, `password_hash` FROM `user_accounts` WHERE `user_name` = :userName");
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
        try{
            $accountDetails = $this->findAccount($userName);
            if ($accountDetails !== false){
                $hash = $accountDetails['password_hash'];
                $id = $accountDetails['id'];
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
                    return $tokens = [
                        'accessToken' => JWTCreator::create($accountDetails),
                        'refreshToken' => (new RTCreator($this->_dbConnector))->create($accountDetails),
                        'expiresIn' => time() - 60
                        /**
                         * По факту в клиент будет улетать время окончания на минуту меньше чем на самом деле.
                         */
                    ];
                }
                return new JsonResponse(['Forbidden' => 'Пароль не верен.'], 403);
            }
            return new JsonResponse(['Forbidden' => 'Такой учетной записи не существует в системе'], 403);
        }catch (\Exception $e){
            //Можно так же логировать ошибку
            //return new JsonResponse(['Auth error' => $e->getMessage()], 500);
        }
    }

}