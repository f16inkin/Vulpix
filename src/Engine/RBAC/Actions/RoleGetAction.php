<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\Core\DataStructures\Entity\HttpResultContainer;
use Vulpix\Engine\RBAC\Domains\Permissions\PermissionManager;
use Vulpix\Engine\RBAC\Domains\Roles\RoleManager;
use Vulpix\Engine\RBAC\Service\PermissionVerificator;
use Vulpix\Engine\RBAC\Service\RBACExceptionsHandler;
use Vulpix\Engine\RBAC\Responders\RoleGetResponder;

/**
 * Получает информацию по выбранной роли.
 *
 * Class RoleGetAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class RoleGetAction implements RequestHandlerInterface
{
    private const ACCESS_PERMISSION = 'RBAC_ROLE_GET';

    private RoleManager $_roleManager;
    private PermissionManager $_permissionManager;
    private RoleGetResponder $_responder;

    /**
     * RoleGetAction constructor.
     * @param RoleManager $manager
     * @param PermissionManager $permissionManager
     * @param RoleGetResponder $responder
     */
    public function __construct(RoleManager $manager, PermissionManager $permissionManager, RoleGetResponder $responder)
    {
        $this->_roleManager = $manager;
        $this->_permissionManager = $permissionManager;
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
                $roleId = (int)$request->getAttribute('id') ?: null;
                $role = $this->_roleManager->getById($roleId);
                if($roleId = $role->getId()){
                    $permissions = $this->_permissionManager->initPermissions($roleId, $this->_permissionManager::GROUPED);
                    $response = $this->_responder->respond($request, new HttpResultContainer($role->setPermissions($permissions), 200));
                }else{
                    $response = $this->_responder->respond($request, new HttpResultContainer($role, 404));
                }
                return $response;
            }
            return new JsonResponse('Access denied. Вам запрещено просматривать роли.', 403);
        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }
    }
}
