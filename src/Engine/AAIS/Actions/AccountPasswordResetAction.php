<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\AAIS\Domains\Accounts\AccountManager;
use Vulpix\Engine\AAIS\Responders\AccountPasswordResetResponder;
use Vulpix\Engine\AAIS\Service\AAISExceptionsHandler;

/**
 * Class AccountPasswordResetAction
 * @package Vulpix\Engine\AAIS\Actions
 */
class AccountPasswordResetAction implements RequestHandlerInterface
{
    private AccountManager $_manager;
    private AccountPasswordResetResponder $_responder;

    /**
     * AccountPasswordResetAction constructor.
     * @param AccountManager $manager
     * @param AccountPasswordResetResponder $responder
     */
    public function __construct(AccountManager $manager, AccountPasswordResetResponder $responder)
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
            $putData = json_decode(file_get_contents("php://input"),true);
            $result = $this->_manager->resetPassword($putData);
            $response = $this->_responder->respond($request, $result);
            return $response;
        }catch (\Exception $e){
            return (new AAISExceptionsHandler())->handle($e);
        }
    }
}