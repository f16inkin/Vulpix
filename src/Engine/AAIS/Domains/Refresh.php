<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains;


use Vulpix\Engine\AAIS\Exceptions\WrongAccessTokenException;
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
     * @param RTCreator $rtCreator
     * @param ExecutionResponse $executionResponse
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
     * @param string|null $oldToken
     * @param array|null $accountDetails
     * @return bool
     */
    private function validate(? string $oldToken, ? array $accountDetails) : bool {
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
     * Вернет пользовательские данные переданные в старом accessToken.
     *
     * @param string|null $accessToken
     * @return array
     * @throws WrongAccessTokenException
     * @throws WrongParamTypeException
     */
    private function getAccountDetails(? string $accessToken) : array {
        if (!empty($accessToken) && isset($accessToken)){
            [$header, $payload, $signature] = explode(".", $accessToken);
            $accountDetails = json_decode(base64_decode($payload))->user;
            if (isset($accountDetails)){
                return (array)$accountDetails;
            }
            throw new WrongAccessTokenException();
        }
        throw new WrongParamTypeException('В метод переданы параметры с неверным типом. Либо null, empty');
    }

    /**
     * Генерация нового refresh токена.
     *
     * @param string|null $oldToken
     * @param string|null $accessToken
     * @return ExecutionResponse
     * @throws WrongAccessTokenException
     * @throws WrongParamTypeException
     */
    public function refresh(? string $oldToken, ? string $accessToken) : ExecutionResponse{
        /**
         * Я должен сделать проверку пришедшего refresh токена.
         * Если он совпадает с тем что хранится в базе, занчит можно выдавать новую пару токенов.
         * Иначе я должен вернуть пользователю ответ с предложением заного пройти аутентификацию.
         * После повторной аутентификации выйдет нвоая пара токенов, рефрешь токен из которой можно будет без
         * проблем валидировать здесь спуся время окнчания аксес токена.
         */
        $oldToken = Sanitizer::sanitize($oldToken);
        $accountDetails = $this->getAccountDetails($accessToken);
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