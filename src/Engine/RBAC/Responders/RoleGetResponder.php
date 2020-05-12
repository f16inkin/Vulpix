<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Responders;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Vulpix\Engine\Core\DataStructures\Entity\ResultContainer;
use Vulpix\Engine\Core\DataStructures\ExecutionResponse;

/**
 * Class RoleGetResponder
 * @package Vulpix\Engine\RBAC\Responders
 */
class RoleGetResponder
{
    /**
     * @param ServerRequestInterface $request
     * @param ResultContainer $payload
     * @return Response
     */
    public function respond(ServerRequestInterface $request, ResultContainer $payload): Response
    {
        if ($payload->getBody()->getId() === 0 ){
            return new JsonResponse('Роль не найдена на сервере.', 404);
        }
        return new JsonResponse($payload->getBody(), $payload->getStatus());
    }
}
