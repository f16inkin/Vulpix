<?php


namespace Vulpix\Engine\AAIS\Exceptions;


use Throwable;

class WrongAccessTokenException extends \Exception
{
    public function __construct($message = "Передан не верный jwtToken", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}