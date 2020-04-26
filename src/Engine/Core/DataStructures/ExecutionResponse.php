<?php


namespace Vulpix\Engine\Core\DataStructures;

/**
 * Класс обертка для всех ответов от модели к action.
 * Упорядочивает ответ для Responder в виде одной структуры.
 * Теперь проше отдавать response.
 *
 * Class ExecutionResponse
 * @package Vulpix\Engine\Core\DataStructures
 */
class ExecutionResponse
{
    private $_body;
    private $_status;

    /**
     * @param null $body
     * @return ExecutionResponse
     */
    public function setBody($body = null) : ExecutionResponse {
        $this->_body = $body;
        return $this;
    }

    /**
     * @param int $status
     * @return ExecutionResponse
     */
    public function setStatus(int $status = 200) : ExecutionResponse {
        $this->_status = $status;
        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name){
        return $this->$name;
    }
}