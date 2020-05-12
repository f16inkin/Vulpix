<?php

declare(strict_types=1);

namespace Vulpix\Engine\AAIS\DataStructures\ValueObjects;

use Vulpix\Engine\Core\Utility\Assert\Assert;

/**
 * VO - для рефреш токена.
 *
 * Class RefreshToken
 * @package Vulpix\Engine\AAIS\DataStructures\ValueObjects
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