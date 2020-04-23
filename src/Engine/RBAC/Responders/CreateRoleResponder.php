<?php


namespace Vulpix\Engine\RBAC\Responders;


use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

class CreateRoleResponder
{
    public function respond(ServerRequestInterface $request, $payload = null): Response
    {
        $response = new JsonResponse($payload['resource'], $payload['status']);
        /**
         * Местоположение записываю в Location, при условии что ресурс создан.
         */
        if (isset($payload['resource'])){
            $response = $response->withHeader('Location', '/api/v1/roles/'.$payload['resource']->_id);
        }
        return $response;
    }
}
