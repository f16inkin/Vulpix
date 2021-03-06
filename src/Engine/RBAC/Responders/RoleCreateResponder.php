<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Responders;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Vulpix\Engine\Core\DataStructures\Entity\HttpResultContainer;

/**
 * Class RoleCreateResponder
 * @package Vulpix\Engine\RBAC\Responders
 */
class RoleCreateResponder
{
    /**
     * @param ServerRequestInterface $request
     * @param HttpResultContainer $payload
     * @return Response
     */
    public function respond(ServerRequestInterface $request, HttpResultContainer $payload): Response
    {
        $response = new JsonResponse($payload->getBody(), $payload->getStatus());
        /**
         * Местоположение записываю в Location, при условии что ресурс создан.
         */
        if ($payload->getStatus() === 201){
            $response = $response->withHeader('Location', '/api/v1/roles/'.($payload->getBody())->getId());
        }
        return $response;
    }
}
