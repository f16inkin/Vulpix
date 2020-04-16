<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains;


use Firebase\JWT\JWT;

/**
 * Класс декоратор создающий JWT.
 *
 * Class JWTCreator
 * @package Vulpix\Engine\AAIS\Domains
 */
class JWTCreator
{
    private function preparePayload(){


    }

    public static function create() : string {
        $secretKey = 'MyTopSecretKey'; //Подгружать из конфигурации

        $jwtPayload = [
            'iss' => "http://example.org1",
            'sub' => '',
            'aud' => "http://example.com",
            'exp' => strtotime('2021-12-01'), //Дата до которой валиден токен
            'nbf' => 1357000000,
            'iat' => 1356999524,
            'jti' => '',
            'user' => [
                'userName' => '',
                'userId' => ''
            ]
        ];

        return JWT::encode($jwtPayload, $secretKey);
    }

}