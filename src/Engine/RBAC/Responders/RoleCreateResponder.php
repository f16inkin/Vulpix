<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Responders;


use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Vulpix\Engine\Core\DataStructures\ExecutionResponse;

class RoleCreateResponder
{
    public function respond(ServerRequestInterface $request, ExecutionResponse $payload): Response
    {
        $response = new JsonResponse($payload->_body, $payload->_status);
        /**
         * Местоположение записываю в Location, при условии что ресурс создан.
         */
        if ($payload->_status === 201){
            $response = $response->withHeader('Location', '/api/v1/roles/'.$payload->_body->_id);
        }
        return $response;
    }
}
