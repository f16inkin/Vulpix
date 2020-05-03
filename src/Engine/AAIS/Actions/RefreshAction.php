<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Actions;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\AAIS\Domains\AAISExceptionsHandler;
use Vulpix\Engine\AAIS\Domains\Refresh;
use Vulpix\Engine\AAIS\Responders\RefreshResponder;

/**
 * Class RefreshAction
 * @package Vulpix\Engine\AAIS\Actions
 */
class RefreshAction implements RequestHandlerInterface
{
    private $_refresh;
    private $_responder;

    /**
     * RefreshAction constructor.
     * @param Refresh $refresh
     * @param RefreshResponder $responder
     */
    public function __construct(Refresh $refresh, RefreshResponder $responder)
    {
        $this->_refresh = $refresh;
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
            $result = $this->_refresh->refresh($postData['refreshToken'], $postData['accessToken']);
            $response = $this->_responder->respond($request, $result);
            return $response;
        }catch (\Exception $e){
            return (new AAISExceptionsHandler())->handle($e);
        }

    }
}