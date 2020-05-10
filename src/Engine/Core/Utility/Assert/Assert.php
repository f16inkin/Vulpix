<?php

declare(strict_types = 1);

namespace Vulpix\Engine\Core\Utility\Assert;

/**
 * Проверка типов
 *
 * Class Assert
 * @package Vulpix\Engine\Core\Utility\Assert
 */
class Assert
{
    public static function isEmpty($value) : bool
    {
        if (empty($value)){
            return true;
        }
        throw new \InvalidArgumentException('Значение переданное аргументом не является пустым');
    }

    public static function notEmpty($value) : bool
    {
        if (!empty($value)){
            return true;
        }
        throw new \InvalidArgumentException('Значение переданное аргументом является пустым');
    }

    public static function isNull($value) : bool
    {
        if (isset($value)){
            return true;
        }
        throw new \InvalidArgumentException('Значение переданное аргументом не является null');
    }

    public static function notNull($value) : bool
    {
        if (!isset($value)){
            return true;
        }
        throw new \InvalidArgumentException('Значение переданное аргументом является null');
    }

}