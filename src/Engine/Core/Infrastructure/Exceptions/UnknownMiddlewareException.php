<?php


namespace Vulpix\Engine\Core\Infrastructure\Exceptions;


use Throwable;

class UnknownMiddlewareException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}