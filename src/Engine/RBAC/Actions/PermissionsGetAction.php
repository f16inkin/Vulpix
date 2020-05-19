<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\Permissions\PermissionManager;
use Vulpix\Engine\RBAC\Service\RBACExceptionsHandler;
use Vulpix\Engine\RBAC\Responders\PermissionsGetResponder;

/**
 * Класс вернет все привелегии системы, разделенные на группы
 *
 * Class PermissionsGetAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class PermissionsGetAction implements RequestHandlerInterface
{
    private $_manager;
    private $_responder;

    /**
     * PermissionsGetAction constructor.
     * @param PermissionManager $manager
     * @param PermissionsGetResponder $responder
     */
    public function __construct(PermissionManager $manager, PermissionsGetResponder $responder)
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
            $getData = json_decode(file_get_contents("php://input"),true) ?: null;
            $result = $this->_manager->getPartly($getData);
            $response = $this->_responder->respond($request, $result);
            return $response;
        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }
    }
}
