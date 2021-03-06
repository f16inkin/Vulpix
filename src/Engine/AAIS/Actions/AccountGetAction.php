<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\AAIS\Domains\Accounts\AccountManager;
use Vulpix\Engine\AAIS\Responders\AccountGetResponder;
use Vulpix\Engine\AAIS\Service\AAISExceptionsHandler;
use Vulpix\Engine\Core\DataStructures\Entity\HttpResultContainer;

/**
 * Class AccountGetAction
 * @package Vulpix\Engine\AAIS\Actions
 */
class AccountGetAction implements RequestHandlerInterface
{
    private AccountManager $_manager;
    private AccountGetResponder $_responder;

    /**
     * AccountGetAction constructor.
     * @param AccountManager $manager
     * @param AccountGetResponder $responder
     */
    public function __construct(AccountManager $manager, AccountGetResponder $responder)
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
            $accountId = (int)$request->getAttribute('id') ?: null;
            $account = $this->_manager->getById($accountId);
            $response = $this->_responder->respond($request, new HttpResultContainer($account, 200));
            return $response;
        }catch (\Exception $e){
            return (new AAISExceptionsHandler())->handle($e);
        }
    }
}