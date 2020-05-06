<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\Core\DataStructures\ExecutionResponse;
use Vulpix\Engine\RBAC\Domains\PermissionCollection;
use Vulpix\Engine\RBAC\Domains\RBACExceptionsHandler;
use Vulpix\Engine\RBAC\Domains\Role;
use Vulpix\Engine\RBAC\Responders\RoleGetResponder;

/**
 * Class RoleGetAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class RoleGetAction implements RequestHandlerInterface
{
    private $_role;
    private $_permissions;
    private $_responder;

    /**
     * RoleGetAction constructor.
     * @param Role $role
     * @param RoleGetResponder $responder
     */
    public function __construct(Role $role, PermissionCollection $permissions, RoleGetResponder $responder)
    {
        $this->_role = $role;
        $this->_permissions = $permissions;
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
            $roleId = (int)$request->getAttribute('id') ?: null;
            $role = $this->_role->get($roleId);
            $permissions = $this->_permissions->initPermissions($roleId);
            $response = $this->_responder->respond($request, (new ExecutionResponse())->setBody($role->setPermissions($permissions))->setStatus(200));
            return $response;
        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }
    }
}
