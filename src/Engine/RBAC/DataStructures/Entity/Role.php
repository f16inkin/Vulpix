<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\DataStructures\Entity;

/**
 * Сущность. Роль.
 *
 * Class Role
 * @package Vulpix\Engine\RBAC\DataStructures\Entity
 */
class Role implements \JsonSerializable
{
    private int $_id;
    private string $_name;
    private string $_description;
    private $_permissions;

    /**
     * Роль может использоватся в пустом виде как заглушка.
     *
     * Role constructor.
     * @param int $id
     * @param string $name
     * @param string $description
     * @param array $permissions
     */
    public function __construct(int $id = 0, $name = '', $description = '')
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_description = $description;
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->_id;
    }

    /**
     * @param $id
     */
    public function setId($id) : void
    {
        $this->_id = $id;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->_name;
    }

    /**
     * @param $name
     */
    public function setName($name) : void
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return $this->_description;
    }

    /**
     * @param $description
     */
    public function setDescription($description) : void
    {
        $this->_description = $description;
    }

    /**
     * @return array
     */
    public function getPermissions(){
        return $this->_permissions;
    }

    /**
     * @param $permissions
     * @return $this
     */
    public function setPermissions($permissions) : Role
    {
        $this->_permissions = $permissions;
        return $this;
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
            'name' => $this->_name,
            'description' => $this->_description,
            'permissions' => $this->_permissions
        ];
    }
}