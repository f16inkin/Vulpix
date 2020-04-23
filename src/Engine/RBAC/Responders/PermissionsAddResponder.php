<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Responders;


use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

class PermissionsAddResponder
{
    public function respond(ServerRequestInterface $request, $payload = null): Response
    {
        return new JsonResponse($payload, 201);
    }
}
