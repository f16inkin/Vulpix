<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\AAIS\Domains\Accounts\AccountManager;
use Vulpix\Engine\AAIS\Responders\AccountCreateResponder;
use Vulpix\Engine\AAIS\Service\AAISExceptionsHandler;

/**
 * Class AccountCreateAction
 * @package Vulpix\Engine\AAIS\Actions
 */
class AccountCreateAction implements RequestHandlerInterface
{
    private AccountManager $_manager;
    private AccountCreateResponder $_responder;

    /**
     * AccountCreateAction constructor.
     * @param AccountManager $manager
     * @param AccountCreateResponder $responder
     */
    public function __construct(AccountManager $manager, AccountCreateResponder $responder)
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
            $postData = json_decode(file_get_contents("php://input"),true) ?: null;
            $result = $this->_manager->create($postData);
            $account = $this->_manager->getById($result->getBody());
            $response = $this->_responder->respond($request, $result->setBody($account));
            return $response;
        }catch (\Exception $e){
            return (new AAISExceptionsHandler())->handle($e);
        }
    }
}