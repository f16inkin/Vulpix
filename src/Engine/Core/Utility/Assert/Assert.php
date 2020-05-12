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
    /**
     * @param $value
     * @return bool
     */
    public static function isEmpty($value) : bool
    {
        if (empty($value)){
            return true;
        }
        throw new \InvalidArgumentException('Значение переданное аргументом не является пустым');
    }

    /**
     * @param $value
     * @return bool
     */
    public static function notEmpty($value) : bool
    {
        if (!empty($value)){
            return true;
        }
        throw new \InvalidArgumentException('Значение переданное аргументом является пустым');
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isNull($value) : bool
    {
        if (!isset($value)){
            return true;
        }
        throw new \InvalidArgumentException('Значение переданное аргументом не является null');
    }

    /**
     * @param $value
     * @return bool
     */
    public static function notNull($value) : bool
    {
        if (isset($value)){
            return true;
        }
        throw new \InvalidArgumentException('Значение переданное аргументом является null');
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isString($value) : bool
    {
        if(is_string($value)){
            return true;
        }
        throw new \InvalidArgumentException('Значение переданное аргументом не является строкой');
    }

    /**
     * @param $value
     * @return bool
     */
    public static function notString($value) : bool
    {
        if(!is_string($value)){
            return true;
        }
        throw new \InvalidArgumentException('Значение переданное аргументом может быть строкой');
    }

}