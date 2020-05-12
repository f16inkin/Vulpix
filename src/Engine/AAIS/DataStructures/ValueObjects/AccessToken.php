<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\DataStructures\ValueObjects;

use Vulpix\Engine\Core\Utility\Assert\Assert;

/**
 * VO - для Access токена.
 * Class AccessToken
 * @package Vulpix\Engine\AAIS\DataStructures\ValueObjects
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