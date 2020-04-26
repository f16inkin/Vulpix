<?php


namespace Vulpix\Engine\AAIS\Responders;


use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Vulpix\Engine\Core\DataStructures\ExecutionResponse;

class RefreshResponder
{
    public function respond(ServerRequestInterface $request, ExecutionResponse $payload): Response
    {
        return new JsonResponse($payload->_body, $payload->_status);
    }
}