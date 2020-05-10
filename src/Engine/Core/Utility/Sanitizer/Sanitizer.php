<?php

declare(strict_types = 1);

namespace Vulpix\Engine\Core\Utility\Sanitizer;

use Vulpix\Engine\Core\Utility\Assert\Assert;

/**
 * Санитизация параметров.
 * !NOTE: добавить рекурсивный обход по массиву.
 * Разработать обработку параметра WhiteList для того, чтобы можно было формировать нвоое регулярное выражение
 * на основе символов из WhiteList.
 *
 * Class Sanitizer
 * @package Vulpix\Engine\Core\Utility\Sanitizer
 */
class Sanitizer
{
    /**
     * @param $structure
     * @param string $whiteList
     * @return string|string[]|null
     */
    public static function sanitize($structure, string $whiteList = ''){
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
     * @return int|string|string[]|null
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