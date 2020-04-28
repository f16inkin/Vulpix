<?php


namespace Vulpix\Engine\Core\Utility\ExceptionsHandler;


use Laminas\Diactoros\Response\JsonResponse;

abstract class ExceptionsHandler
{
    protected function unhandled() : JsonResponse {
        return new JsonResponse('Данное исключение пока не обработано',500);
    }

    abstract public function handle(\Exception $e);

}