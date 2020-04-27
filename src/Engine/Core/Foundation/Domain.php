<?php


namespace Vulpix\Engine\Core\Foundation;


class Domain
{

    /**
     * Очищает входные параметры от спец символов и пробелов
     *
     * @param $structure
     * @return array|string|string[]|null
     * @throws WrongParamsTypeException
     */
    protected function sanitize($structure){
        if (!empty($structure && isset($structure))){
            if (is_array($structure)){
                $sanitized = [];
                foreach ($structure as $key => $value) {
                    $convertedString = mb_convert_encoding($value, "utf-8");
                    $sanitized[$key] = preg_replace ("/[^a-zA-ZА-Яа-я0-9\s-]/ui","", $convertedString);
                }
            }elseif (is_int($structure)){
                $sanitized = (int)preg_replace ("/[^a-zA-ZА-Яа-я0-9\s-]/ui","", $structure);
            }
            else{
                $convertedString = mb_convert_encoding($structure, "utf-8");
                $sanitized = preg_replace ("/[^a-zA-ZА-Яа-я0-9\s-]/ui","", $convertedString);
            }

            return $sanitized;
        }
        throw new WrongParamsTypeException('Не верные параметры для санитизации');
    }

}
