<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\Permission;
use Vulpix\Engine\RBAC\Domains\RBACExceptionsHandler;
use Vulpix\Engine\RBAC\Responders\PermissionsGetDiffResponder;

/**
 * Класс для поиска тех привелегий которые отсутсвуют в текущей роли.
 *
 * Class PermissionsGetDiffAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class PermissionsGetDiffAction implements RequestHandlerInterface
{
    private $_permission;
    private $_responder;

    /**
     * PermissionsGetDiffAction constructor.
     * @param Permission $permission
     * @param PermissionsGetDiffResponder $responder
     */
    public function __construct(Permission $permission, PermissionsGetDiffResponder $responder)
    {
        $this->_permission = $permission;
        $this->_responder = $responder;
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * Ищет разницу для не существующих ролей
         * Другими словами если будет задана в параметр роль с несуществующим ID, метод вернет в ответ все привелегии.
         * На контроль доступа это никак не повлияет, так как данный метод всего лишь выводит те привелегии которые
         * можно добавить (как разницу между имеющимися у пользователя и доступными в системе).
         * Инициализации и проверка привелегии для контроля доступа проходит в Middleware и Actions
         */
        try{
            $getData = json_decode(file_get_contents("php://input"),true) ?: null;
            $roleId = (int)$getData['roleId'] ?: null; //$request->getAttribute('getParams')['roleId'];
            $differentPermissions = $this->_permission->getDifferentPermissions($roleId);
            $response = $this->_responder->respond($request, $differentPermissions);
            return $response;
        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }
    }
}
