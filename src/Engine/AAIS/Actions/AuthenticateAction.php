<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Actions;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\AAIS\Domains\Authentication;
use Vulpix\Engine\AAIS\Responders\AuthenticateResponder;

class AuthenticateAction implements RequestHandlerInterface
{
    private $_authentication;
    private $_responder;

    public function __construct(Authentication $authentication, AuthenticateResponder $responder)
    {
        $this->_authentication = $authentication;
        $this->_responder = $responder;
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $credentials = json_decode(file_get_contents("php://input"),true);
        $tokens = $this->_authentication->authenticate($credentials['userName'], $credentials['userPassword']);
        $response = $this->_responder->respond($request, $tokens);
        return $response;
    }
}