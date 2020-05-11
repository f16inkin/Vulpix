<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Responders;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Vulpix\Engine\Core\DataStructures\Entity\ResultContainer;

/**
 * Class PermissionsDeleteResponder
 * @package Vulpix\Engine\RBAC\Responders
 */
class PermissionsDeleteResponder
{
    /**
     * @param ServerRequestInterface $request
     * @param ResultContainer $payload
     * @return Response
     */
    public function respond(ServerRequestInterface $request, ResultContainer $payload): Response
    {
        return new JsonResponse($payload->getBody(), $payload->getStatus());
    }
}
