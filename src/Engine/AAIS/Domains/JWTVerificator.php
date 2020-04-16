<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains;


use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Проверяет токен.
 *
 * Class JWTVerificator
 * @package Vulpix\Engine\AAIS\Domains
 */
class JWTVerificator
{
    public static function verify(ServerRequestInterface $request) : bool {
        try{
            $encodedToken = mb_substr(($request->getHeader('authorization')[0]), 7);

            $secretKey = 'MyTopSecretKey'; //Подгружать из конфигурации

            JWT::decode($encodedToken,$secretKey,['HS256']);
            return true;
        }catch (SignatureInvalidException $e){
            return false;
        }



    }
}