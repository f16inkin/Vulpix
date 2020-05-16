<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains\Tokens;

use Vulpix\Engine\Core\Utility\Assert\Assert;

/**
 * Value Object.
 *
 * Class RefreshToken
 * @package Vulpix\Engine\AAIS\Domains\Tokens
 */
class RefreshToken
{
    private string $_value;

    /**
     * RefreshToken constructor.
     * @param $value
     */
    public function __construct($value)
    {
        Assert::notNull($value);
        Assert::notEmpty($value);
        $this->_value = (string)$value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }
}