<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains;


use Vulpix\Engine\Core\DataStructures\ExecutionResponse;
use Vulpix\Engine\Core\Utility\Sanitizer\Exceptions\WrongParamTypeException;
use Vulpix\Engine\Core\Utility\Sanitizer\Sanitizer;
use Vulpix\Engine\Database\Connectors\IConnector;

/**
 * Обновляет refresh токен
 *
 * Class Refresh
 * @package Vulpix\Engine\AAIS\Domains
 */
class Refresh
{
    private $_dbConnection;
    private $_rtCreator;
    private $_executionResponse;

    /**
     * Refresh constructor.
     * @param IConnector $_dbConnector
     */
    public function __construct(IConnector $_dbConnector, RTCreator $rtCreator, ExecutionResponse $executionResponse)
    {
        $this->_dbConnection = $_dbConnector::getConnection();
        $this->_rtCreator = $rtCreator;
        $this->_executionResponse = $executionResponse;
    }

    /**
     * Проверяет пришедший токен.
     * Если токен который пришел есть в базе данных для текущего пользователя, значит валидация прошла
     * и можно выдавать новую пару ключей.
     *
     * @param string $oldToken
     * @param int $userId
     * @return bool
     */
    private function validate(string $oldToken, ? array $accountDetails) : bool {
        $query = ("SELECT * FROM `refresh_tokens` WHERE `token` = :oldToken AND `user_id` = :userId");
        $result = $this->_dbConnection->prepare($query);
        $result->execute([
            'oldToken' => $oldToken,
            'userId' => $accountDetails['userId']
        ]);
        if ($result->rowCount() > 0){
            return true;
        }
        return false;
    }

    /**
     * Генерация нового refresh токена
     *
     * @param string $oldToken
     * @param array $accountDetails
     * @return ExecutionResponse
     * @throws WrongParamTypeException
     */
    public function refresh(string $oldToken, ? array $accountDetails) : ExecutionResponse{
        /**
         * Я должен сделать проверку пришедшего refresh токена.
         * Если он совпадает с тем что хранится в базе, занчит можно выдавать новую пару токенов.
         * Иначе я должен вернуть пользователю ответ с предложением заного пройти аутентификацию.
         * После повторной аутентификации выйдет нвоая пара токенов, рефрешь токен из которой можно будет без
         * проблем валидировать здесь спуся время окнчания аксес токена.
         */
        $oldToken = Sanitizer::sanitize($oldToken);
        $accountDetails = Sanitizer::sanitize($accountDetails);
        if ($this->validate($oldToken, $accountDetails)){
            $tokens = [
                'accessToken' => JWTCreator::create($accountDetails),
                'refreshToken' => $this->_rtCreator->create($accountDetails),
                'expiresIn' => JWTCreator::getExpiresIn() - 60
            ];
            return $this->_executionResponse->setBody($tokens)->setStatus(200);
        }
        /**
         * Токен может не пройти валидацию в двух случаях:
         * 1) Токен устарел
         * 2) Не переданы пользовтаельские данные для идентификации токена
         */
        return $this->_executionResponse->setBody('Данный токен не прошел валидацию')->setStatus(401);
    }

}