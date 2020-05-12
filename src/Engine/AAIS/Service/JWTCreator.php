<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Service;

use Firebase\JWT\JWT;

/**
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
    private static function preparePayload(array $accountDetails) : array {
        $payload = self::loadConfigs()['payload'];
        $payload['user']['userName'] = $accountDetails['userName'];
        $payload['user']['userId'] = $accountDetails['userId'];
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
    public static function create(array $accountDetails) : string {
        $jwtPayload = self::preparePayload($accountDetails);
        $secretKey = self::getSecretKey();
        return JWT::encode($jwtPayload, $secretKey);
    }

}