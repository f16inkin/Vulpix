<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains\Tokens;

use Throwable;

/**
 * Class UnexpectedTokenException
 * @package Vulpix\Engine\AAIS\Domains\Tokens
 */
class UnexpectedTokenException extends \Exception
{
    /**
     * UnexpectedTokenException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}