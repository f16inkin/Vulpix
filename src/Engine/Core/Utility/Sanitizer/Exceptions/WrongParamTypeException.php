<?php


namespace Vulpix\Engine\Core\Utility\Sanitizer\Exceptions;


use Throwable;

class WrongParamTypeException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}