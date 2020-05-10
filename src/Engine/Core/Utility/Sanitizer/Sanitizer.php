<?php

declare(strict_types = 1);

namespace Vulpix\Engine\Core\Utility\Sanitizer;

use Vulpix\Engine\Core\Utility\Assert\Assert;
use Vulpix\Engine\Core\Utility\Sanitizer\Exceptions\WrongParamTypeException;

/**
 * Очищает от спецсимволов одномерные массивы, строки, числа.
 *
 * Class Sanitizer
 * @package Vulpix\Engine\Core\Utility\Sanitizer
 */
class Sanitizer
{
    /**
     * @param $structure
     * @return array|string
     * @throws WrongParamTypeException
     */
    public static function sanitize($structure){
        Assert::notEmpty($structure);
        Assert::notNull($structure);
        if (is_array($structure)){
            foreach ($structure as $key => $value) {
                $convertedString = mb_convert_encoding($value, "utf-8");
                $sanitized[$key] = preg_replace ("/[^a-zA-ZА-Яа-я0-9\s-]/ui","", $convertedString);
            }
        }else{
            $convertedString = mb_convert_encoding($structure, "utf-8");
            $sanitized = preg_replace ("/[^a-zA-ZА-Яа-я0-9\s-]/ui","", $convertedString);
            return $sanitized;
        }
        return $sanitized;
    }

    /**
     * @param $structure
     * @return int
     * @throws WrongParamTypeException
     */
    public static function transformToInt($structure){
        Assert::notEmpty($structure);
        Assert::notNull($structure);
        if (is_array($structure)){
            foreach ($structure as $key => $value) {
                $sanitized[$key] = (int) preg_replace ("/[^0-9]/","", $structure);
            }
            return $sanitized;
        }else{
            $sanitized = preg_replace ("/[^0-9]/","", $structure);
            return (int) $sanitized;
        }
    }

}