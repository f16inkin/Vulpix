<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Middleware;


use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\AAIS\Domains\Authentication;
use Vulpix\Engine\AAIS\Domains\JWTVerificator;
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
        /**
         * 1) Проверка JWT. Если JWT есть и пройдена валидация Пропуск по дальнейшему пути.
         * 2) Если JWT по каким то причинам не подходит или отсутствует, вернем ответ о необходимости
         * повторной аутентификации. Заново ввести логин и пароль и получить новый jwt токен.
         *
         */

        $token = (new Authentication($this->_dbConnector))->authenticate('Mikki', '12345');
        $request = $request->withAttribute('jwt', $token);

        if (JWTVerificator::verify($request)){
            $response = $handler->handle($request);
        }
        else{
            $response = new JsonResponse('Unauthorized', 401);
        }



        return $response;
    }
}