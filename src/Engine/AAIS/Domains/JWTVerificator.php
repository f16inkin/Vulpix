<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains;


use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;

/**
 * Проверяет токен.
 *
 * Class JWTVerificator
 * @package Vulpix\Engine\AAIS\Domains
 */
class JWTVerificator
{
    public static function verify(string $accessToken) : bool {
        try{
            $secretKey = JWTCreator::getSecretKey();
            JWT::decode($accessToken, $secretKey, ['HS256']);
            return true;
        }catch (SignatureInvalidException $e){
            return false;
        }



    }
}