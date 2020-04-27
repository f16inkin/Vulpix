<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Responders;


use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Vulpix\Engine\Core\DataStructures\ExecutionResponse;

/**
 * Class RoleGetAllResponder
 * @package Vulpix\Engine\RBAC\Responders
 */
class RoleGetAllResponder
{
    /**
     * @param ServerRequestInterface $request
     * @param ExecutionResponse $payload
     * @return Response
     */
    public function respond(ServerRequestInterface $request, ExecutionResponse $payload): Response
    {
        return $response = new JsonResponse($payload->_body, $payload->_status);
    }

}
