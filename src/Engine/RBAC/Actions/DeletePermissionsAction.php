<?php


namespace Vulpix\Engine\RBAC\Actions;


use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\PermissionManager;
use Vulpix\Engine\RBAC\Responders\DeletePermissionsResponder;

/**
 * Class DeletePermissionsAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class DeletePermissionsAction implements RequestHandlerInterface
{
    private $_manager;
    private $_responder;

    /**
     * DeletePermissionsAction constructor.
     * @param PermissionManager $manager
     * @param DeletePermissionsResponder $responder
     */
    public function __construct(PermissionManager $manager, DeletePermissionsResponder $responder)
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
            /**
             * Учитывается так же ситуация, когда на маршрут не отправлены параметры.
             * Если так случилось будет брошено PDO исключение.
             */
            $deleteData = json_decode(file_get_contents("php://input"),true);
            $roleId = (int)$deleteData['roleId'];
            //Дабы не ловить ошибку несоответсвия типов в противном случае отправлю пустой массив.
            $deletingPermissionsIDs = $deleteData['permissionIDs'] ?: [];
            $this->_manager->deletePermissions($roleId, $deletingPermissionsIDs);
            return $this->_responder->respond($request);
        }catch (\PDOException $e){
            //В будующем сделаю обработчик ошибок как в Clear Sky. И буду красиво обрабатывать вывод ошибок.
            return new JsonResponse(['Ошибка работы БД' => $e->getMessage()], 500);
        }
    }
}
