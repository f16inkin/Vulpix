<?php


namespace Vulpix\Engine\RBAC\Actions;


use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\Role;
use Vulpix\Engine\RBAC\Responders\CreateRoleResponder;

class RoleCreateAction implements RequestHandlerInterface
{
    private $_role;
    private $_responder;

    public function __construct(Role $role, CreateRoleResponder $responder)
    {
        $this->_role = $role;
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
            $postData = json_decode(file_get_contents("php://input"),true);
            $roleName = $postData['roleName'] ?: '';
            $roleDescription = $postData['roleDescription'];
            /**
             * Если параметры пустые или null сервер ответи 400 - Bad Request
             */
            if ($roleName !== '' AND $roleName !== null){
                /**
                 * Если роль уже присутсвует сервер ответит 409 - Conflict
                 */
                if (!$this->_role->isROleExist($roleName)){
                    /**
                     * Иначе вернет 201 - Created и сразу созданный ресурс, а так же ссылку на него.
                     */
                    $roleID = $this->_role->create($roleName, $roleDescription);
                    $role = $this->_role->read($roleID);
                    $payload['status'] = 201;
                    $payload['resource'] = $role;
                }else{
                    $payload['status'] = 409;
                }
            }else{
                $payload['status'] = 400;
            }
            $response = $this->_responder->respond($request, $payload);
            return $response;
        }catch (\PDOException $e){
            return new JsonResponse(['Ошибка работы БД' => $e->getMessage()], 500);
        }

    }
}
