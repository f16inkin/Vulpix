<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Responders;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Vulpix\Engine\Core\DataStructures\Entity\HttpResultContainer;

/**
 * Class AccountPasswordResetResponder
 * @package Vulpix\Engine\AAIS\Responders
 */
class AccountPasswordResetResponder
{
    /**
     * @param ServerRequestInterface $request
     * @param HttpResultContainer $payload
     * @return Response
     */
    public function respond(ServerRequestInterface $request, HttpResultContainer $payload): Response
    {
        return new JsonResponse($payload->getBody(), $payload->getStatus());
    }
}