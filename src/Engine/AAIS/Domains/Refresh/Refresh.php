<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains\Refresh;

use Vulpix\Engine\AAIS\Domains\Accounts\Account;
use Vulpix\Engine\AAIS\Domains\Tokens\AccessToken;
use Vulpix\Engine\AAIS\Domains\Tokens\RefreshToken;
use Vulpix\Engine\AAIS\Domains\Tokens\UnexpectedTokenException;
use Vulpix\Engine\AAIS\Service\JWTCreator;
use Vulpix\Engine\AAIS\Service\RTCreator;
use Vulpix\Engine\Core\DataStructures\Entity\HttpResultContainer;
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
    private $_resultContainer;

    /**
     * Refresh constructor.
     * @param IConnector $_dbConnector
     * @param RTCreator $rtCreator
     * @param HttpResultContainer $resultContainer
     */
    public function __construct(IConnector $_dbConnector, RTCreator $rtCreator, HttpResultContainer $resultContainer)
    {
        $this->_dbConnection = $_dbConnector::getConnection();
        $this->_rtCreator = $rtCreator;
        $this->_resultContainer = $resultContainer;
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
    private function validate(RefreshToken $oldToken, Account $account) : bool {
        $query = ("SELECT * FROM `refresh_tokens` WHERE `token` = :oldToken AND `user_id` = :userId");
        $result = $this->_dbConnection->prepare($query);
        $result->execute([
            'oldToken' => $oldToken->getValue(),
            'userId' => $account->getId()
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
     * @throws UnexpectedTokenException
     */
    private function getAccount(AccessToken $accessToken) : Account {
        [$header, $payload, $signature] = explode(".", $accessToken->getValue());
        if (isset($payload)){
            $accountDetails = json_decode(base64_decode($payload))->user;
            return new Account($accountDetails->userId, $accountDetails->userName);
        }
        throw new UnexpectedTokenException('Передан не верный jwtToken');
    }

    /**
     * Здесь парамтерами должны передаваться VO: RefreshToken и AccessToken.
     * Сами VO уже включают валидацию и проверку вида по регулярному выражению например для RT.
     * @throws UnexpectedTokenException
     */
    public function refresh(RefreshToken $oldToken, AccessToken $accessToken) : HttpResultContainer
    {
        /**
         * Я должен сделать проверку пришедшего refresh токена.
         * Если он совпадает с тем что хранится в базе, занчит можно выдавать новую пару токенов.
         * Иначе я должен вернуть пользователю ответ с предложением заного пройти аутентификацию.
         * После повторной аутентификации выйдет нвоая пара токенов, рефрешь токен из которой можно будет без
         * проблем валидировать здесь спуся время окнчания аксес токена.
         */
        $account = $this->getAccount($accessToken);
        if ($this->validate($oldToken, $account)){
            $tokens = [
                'accessToken' => JWTCreator::create($account),
                'refreshToken' => $this->_rtCreator->create($account),
                'expiresIn' => JWTCreator::getExpiresIn() - 60
            ];
            return $this->_resultContainer->setBody($tokens)->setStatus(200);
        }
        /**
         * Токен может не пройти валидацию в двух случаях:
         * 1) Токен устарел
         * 2) Не переданы пользовательские данные для идентификации токена
         */
        return $this->_resultContainer->setBody('Данный токен не прошел валидацию')->setStatus(401);
    }
}