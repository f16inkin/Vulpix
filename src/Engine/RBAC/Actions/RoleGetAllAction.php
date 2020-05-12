<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\RoleManager;
use Vulpix\Engine\RBAC\Service\PermissionVerificator;
use Vulpix\Engine\RBAC\Service\RBACExceptionsHandler;
use Vulpix\Engine\RBAC\Responders\RoleGetAllResponder;

/**
 * Получить все роли системы.
 *
 * Class RoleGetAllAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class RoleGetAllAction implements RequestHandlerInterface
{
    private const ACCESS_PERMISSION = 'RBAC_ROLES_GET_ALL';

    private $_manager;
    private $_responder;

    /**
     * RoleGetAllAction constructor.
     * @param RoleManager $manager
     * @param RoleGetAllResponder $responder
     */
    public function __construct(RoleManager $manager, RoleGetAllResponder $responder)
    {
        $this->_manager = $manager;
        $this->_responder = $responder;
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try{
            if (PermissionVerificator::verify($request->getAttribute('Roles'), self::ACCESS_PERMISSION)){
                $result = $this->_manager->getAll();
                $response = $this->_responder->respond($request, $result);
                return $response;
            }
            return new JsonResponse('Access denied. Вам запрещено просматривать роли.', 403);
        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }
    }
}
