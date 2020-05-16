<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Service;

use Firebase\JWT\JWT;
use Vulpix\Engine\AAIS\Domains\Accounts\Account;

/**
 * Service.
 *
 * Класс декоратор создающий JWT.
 *
 * Class JWTCreator
 * @package Vulpix\Engine\AAIS\Domains
 */
class JWTCreator
{
    private static $_configs = null;

    /**
     * Одноразовая подгрузка конфигураций для токена
     *
     * @return array
     */
    private static function loadConfigs() : array {
        if (is_null(self::$_configs)){
            self::$_configs = include_once 'configs/token.php';
        }
        return self::$_configs;
    }

    /**
     * Конфигурация полезной нагрузки
     *
     * @param array $accountDetails
     * @return array
     */
    private static function preparePayload(Account $account) : array {
        $payload = self::loadConfigs()['payload'];
        $payload['user']['userName'] = $account->getUserName();
        $payload['user']['userId'] = $account->getId();
        return $payload;
    }

    /**
     * Единая точка для доступа к секретному ключу для Signature for JWT
     *
     * @return string
     */
    public static function getSecretKey() : string {
        $secretKey = self::loadConfigs()['secretKey'];
        return $secretKey;
    }

    /**
     * Вернет время окончания действия токена из настроек
     *
     * @return int
     */
    public static function getExpiresIn() : int {
        $expiresIn = self::loadConfigs()['payload']['exp'];
        return $expiresIn;
    }

    /**
     * Creates JWT / access token with custom payload
     *
     * @param array $accountDetails
     * @return string
     */
    public static function create(Account $account) : string {
        $jwtPayload = self::preparePayload($account);
        $secretKey = self::getSecretKey();
        return JWT::encode($jwtPayload, $secretKey);
    }

}