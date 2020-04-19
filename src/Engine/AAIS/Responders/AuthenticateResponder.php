<?php


namespace Vulpix\Engine\AAIS\Responders;


use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticateResponder
{
    public function respond(ServerRequestInterface $request, $payload = null): Response
    {
        return new JsonResponse($payload);
    }
}