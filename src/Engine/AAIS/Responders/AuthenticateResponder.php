<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Responders;


use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Vulpix\Engine\Core\DataStructures\ExecutionResponse;

class AuthenticateResponder
{
    /**
     * @param ServerRequestInterface $request
     * @param ExecutionResponse $payload
     * @return Response
     */
    public function respond(ServerRequestInterface $request, ExecutionResponse $payload): Response
    {
        return new JsonResponse($payload->_body, $payload->_status);
    }
}