<?php

declare(strict_types = 1);

namespace Vulpix\Engine\Core\DataStructures\Entity;

/**
 * Класс заворачивает в себя структуированный ответ. И служит для передачи данных из доменной модели в контроллеры.
 * Далее сериализуется в JSON и отправляется клиенту.
 *
 * Class HttpResultContainer
 * @package Vulpix\Engine\Core\DataStructures\Entity
 */
class HttpResultContainer
{
    private $_body;
    private int $_status;

    /**
     * Дефолтное состояние объекта 204 - без контента.
     *
     * HttpResultContainer constructor.
     * @param null $_body
     * @param int $status
     */
    public function __construct($_body = null, int $status = 204)
    {
        $this->_body = $_body;
        $this->_status = $status;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * @param null $body
     */
    public function setBody($body) : HttpResultContainer
    {
        $this->_body = $body;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus() : int
    {
        return $this->_status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status) : HttpResultContainer
    {
        $this->_status = $status;
        return $this;
    }


}