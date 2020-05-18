<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains\Accounts;

/**
 * Entity.
 *
 * Class Account
 * @package Vulpix\Engine\AAIS\Domains\Accounts
 */
class Account implements \JsonSerializable
{
    private int $_id;
    private string $_userName;
    private string $_passwordHash;

    /**
     * Account constructor.
     * @param int $id
     * @param string $userName
     * @param string $passwordHash
     */
    public function __construct(int $id = 0, string $userName = '', string $passwordHash = '')
    {
        $this->_id = $id;
        $this->_userName = $userName;
        $this->_passwordHash = $passwordHash;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_id > 0  ? $this->_id : false;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->_userName;
    }

    /**
     * @return mixed
     */
    public function getPasswordHash()
    {
        return $this->_passwordHash;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->_id,
            'name' => $this->_userName
        ];
    }
}