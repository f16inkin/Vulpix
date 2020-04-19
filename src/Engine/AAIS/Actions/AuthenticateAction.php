<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Actions;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\AAIS\Domains\Authentication;
use Vulpix\Engine\AAIS\Responders\AuthenticateResponder;
use Vulpix\Engine\Database\Connectors\IConnector;

class AuthenticateAction implements RequestHandlerInterface
{
    private $_dbConnector;
    private $_responder;

    public function __construct(IConnector $dbConnector, AuthenticateResponder $responder)
    {
        $this->_dbConnector = $dbConnector;
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
        $tokens = (new Authentication($this->_dbConnector))->authenticate($credentials['userName'], $credentials['userPassword']);
        $response = $this->_responder->respond($request, $tokens);
        return $response;
    }
}