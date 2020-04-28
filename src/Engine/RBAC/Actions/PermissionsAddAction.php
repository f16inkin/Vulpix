<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
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
    private $_responder;

    /**
     * PermissionsAddAction constructor.
     * @param PermissionManager $manager
     * @param Role $role
     * @param PermissionsAddResponder $responder
     */
    public function __construct(PermissionManager $manager, Role $role, PermissionsAddResponder $responder)
    {
        $this->_manager = $manager;
        $this->_role = $role;
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
            /**
             * Учитывается так же ситуация, когда на маршрут не отправлены параметры.
             * Если так случилось будет возврашен JSON с NULL параметрами.
             */
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
             * были ли добавлены првиелегии, либо запрос прошел и добавляемые првиелегии уже были у роли.
             */
            $exec = $this->_manager->addPermissions($roleId, $permissionsIDs);
            //Полная ифнормация по роли
            $role = $this->_role->get($exec->_body);
            $response = $this->_responder->respond($request, $exec->setBody($role));
            return $response;
        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }
    }
}
