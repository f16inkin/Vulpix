<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Responders;


use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Vulpix\Engine\Core\DataStructures\ExecutionResponse;

/**
 * Class RoleGetResponder
 * @package Vulpix\Engine\RBAC\Responders
 */
class RoleGetResponder
{
    public function respond(ServerRequestInterface $request, ExecutionResponse $payload): Response
    {
        /**
         * Считается важным ничего не возвращать на запрос с неверным параметром, либо с id отсутсвующем в БД.
         */
        if ($payload->_body->_id === null){
            $payload->setStatus(204);
        }
        return $response = new JsonResponse($payload->_body, $payload->_status);
    }

}
