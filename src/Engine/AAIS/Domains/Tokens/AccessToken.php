<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains\Tokens;

use Vulpix\Engine\Core\Utility\Assert\Assert;

/**
 * Value Object.
 *
 * Class AccessToken
 * @package Vulpix\Engine\AAIS\Domains\Tokens
 */
class AccessToken
{
    private string $_value;

    /**
     * AccessToken constructor.
     * @param $value
     */
    public function __construct($value)
    {
        Assert::notNull($value);
        Assert::notEmpty($value);
        $this->_value = (string)$value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->_value;
    }
}