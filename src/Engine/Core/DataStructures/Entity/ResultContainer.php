<?php

declare(strict_types=1);

namespace Vulpix\Engine\Core\DataStructures\Entity;

/**
 * Весь смысл класса в том чтобы передавать данные между слоями приложения по одной определенной структуре.
 *
 * Class ResultContainer
 * @package Vulpix\Engine\Core\DataStructures\Entity
 */
class ResultContainer
{
    private $_body;
    private int $_status;

    /**
     * Дефолтное состояние объекта 204 - без контента.
     *
     * ResultContainer constructor.
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
    public function setBody($body) : ResultContainer
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
    public function setStatus(int $status) : ResultContainer
    {
        $this->_status = $status;
        return $this;
    }


}