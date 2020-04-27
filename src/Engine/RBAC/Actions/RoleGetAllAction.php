<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\RBACExceptionsHandler;
use Vulpix\Engine\RBAC\Domains\Role;
use Vulpix\Engine\RBAC\Responders\RoleGetAllResponder;

/**
 * Class RoleGetAllAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class RoleGetAllAction implements RequestHandlerInterface
{
    private $_role;
    private $_responder;

    /**
     * RoleGetAllAction constructor.
     * @param Role $role
     * @param RoleGetAllResponder $responder
     */
    public function __construct(Role $role, RoleGetAllResponder $responder)
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
            $exec = $this->_role->getAll();
            $response = $this->_responder->respond($request, $exec);
            return $response;
        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }
    }
}
