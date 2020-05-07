<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\PermissionCollection;
use Vulpix\Engine\RBAC\Domains\PermissionManager;
use Vulpix\Engine\RBAC\Domains\RBACExceptionsHandler;
use Vulpix\Engine\RBAC\Domains\Role;
use Vulpix\Engine\RBAC\Responders\PermissionsAddResponder;

/**
 * Class PermissionsAddAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class PermissionsAddAction implements RequestHandlerInterface
{
    private $_manager;
    private $_role;
    private $_permissions;
    private $_responder;

    /**
     * PermissionsAddAction constructor.
     * @param PermissionManager $manager
     * @param Role $role
     * @param PermissionsAddResponder $responder
     */
    public function __construct(PermissionManager $manager, PermissionCollection $permissions, Role $role, PermissionsAddResponder $responder)
    {
        $this->_manager = $manager;
        $this->_role = $role;
        $this->_permissions = $permissions;
        $this->_responder = $responder;
    }

    /**
     * По итогу исполнения вернет Роль в которую добавлялись привелегии в независимости от результата.
     * Добавились или нет.
     *
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try{
            $postData = json_decode(file_get_contents("php://input"),true) ?: null;
            $roleId = (int)$postData['roleId'] ?: null;
            //Добавляемые привелегии
            $addingPermissionsIDs = $postData['permissionIDs'];
            //Найденные привелегии для текущей роли
            $foundPermissionIDs = $this->_manager->findRolePermissionIDs($roleId, $addingPermissionsIDs);
            //Те привелегии которые добавляются, которых нет в имеющихся
            $permissionsIDs = array_diff($addingPermissionsIDs, $foundPermissionIDs);
            /**
             * Вернет либо 201 либо 200 статус в результате выполнения. На клиенете будет проще различать по статусу
             * были ли добавлены привелегии, либо запрос прошел и добавляемые првиелегии уже были у роли.
             */
            $exec = $this->_manager->addPermissions($roleId, $permissionsIDs);
            //Полная ифнормация по роли
            $role = $this->_role->get($exec->_body);
            $permissions = $this->_permissions->initPermissions($roleId);
            $response = $this->_responder->respond($request, $exec->setBody($role->setPermissions($permissions)));
            return $response;
        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }
    }
}
