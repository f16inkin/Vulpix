<?php

/**
 * [iss] - чувствительная к регистру строка или URI, которая является уникальным идентификатором стороны,
 * генерирующей токен (issuer).
 * [sub] - чувствительная к регистру строка или URI, которая является уникальным идентификатором стороны,
 * о которой содержится информация в данном токене (subject). Значения с этим ключом должны быть уникальны
 * в контексте стороны, генерирующей JWT.
 * [aud] - массив чувствительных к регистру строк или URI, являющийся списком получателей данного токена.
 * Когда принимающая сторона получает JWT с данным ключом, она должна проверить наличие себя в
 * получателях - иначе проигнорировать токен (audience)/
 * [exp] - время в формате Unix Time, определяющее момент, когда токен станет не валидным (expiration).
 * [nbf] - в противоположность ключу exp, это время в формате Unix Time, определяющее момент, когда токен
 * станет валидным (not before).
 * [iat] -  время в формате Unix Time, определяющее момент, когда токен был создан. iat и nbf могут
 * не совпадать, например, если токен был создан раньше, чем время, когда он должен стать валидным.
 * [jti] - строка, определяющая уникальный идентификатор данного токена (JWT ID).
 */

return [
    'payload' => [
        'iss' => 'http://example.org',
        'sub' => '', //
        'aud' => 'http://example.com',
        'exp' => time() + (24 * 60 * 60 * 365),
        'nbf' => time(),
        'iat' => time(),
        'jti' => '',
        'user' => []
    ],
    'secretKey' => 'MyTopSecretKey'
];