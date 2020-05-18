<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\AAIS\Domains\Accounts\AccountManager;
use Vulpix\Engine\AAIS\Responders\AccountsGetResponder;
use Vulpix\Engine\AAIS\Service\AAISExceptionsHandler;

/**
 * Class AccountsGetAction
 * @package Vulpix\Engine\AAIS\Actions
 */
class AccountsGetAction implements RequestHandlerInterface
{
    private AccountManager $_manager;
    private AccountsGetResponder $_responder;

    /**
     * AccountsGetAction constructor.
     * @param AccountManager $manager
     * @param AccountsGetResponder $responder
     */
    public function __construct(AccountManager $manager, AccountsGetResponder $responder)
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
            return (new AAISExceptionsHandler())->handle($e);
        }
    }
}