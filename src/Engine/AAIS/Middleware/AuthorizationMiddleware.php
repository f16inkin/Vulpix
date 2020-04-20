<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Middleware;


use Firebase\JWT\JWT;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\AAIS\Domains\JWTCreator;
use Vulpix\Engine\Database\Connectors\IConnector;

/**
 * Авторизация - проверка прав пользователя на ДОСТУП к определенным ресурсам.
 *
 * Class AuthorizationMiddleware
 * @package Vulpix\Engine\AAIS\Middleware
 */
class AuthorizationMiddleware implements MiddlewareInterface
{
    private $_dbConnector;

    /**
     * AuthorizationMiddleware constructor.
     * @param IConnector $dbConnector
     */
    public function __construct(IConnector $dbConnector)
    {
        $this->_dbConnector = $dbConnector;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try{
            /**
             * В случае если токен был передан в заголовках
             */
            if (!empty($request->getHeader('authorization'))){
                $accessToken = mb_substr(($request->getHeader('authorization')[0]), 7);
                $secretKey = JWTCreator::getSecretKey();
                $user = (JWT::decode($accessToken, $secretKey, ['HS256']))->user;
                $response = $handler->handle($request = $request->withAttribute('User', $user));
            }
            /**
             * Клиент должен обработать 401 статус, перенаправив на авторизацию /authenticate
             */
            else{
                return new JsonResponse(['Unauthorized' => 'Token has not been found'], 401);
            }
            return $response;
        }catch (\Exception $e){
            return new JsonResponse(['Unauthorized' => $e->getMessage()], 401);
        }
    }
}