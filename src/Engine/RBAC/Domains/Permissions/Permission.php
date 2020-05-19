<?php

declare (strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains\Permissions;

/**
 * Entity.
 *
 * Class Permission
 * @package Vulpix\Engine\RBAC\Domains\Permissions
 */
class Permission implements \JsonSerializable
{
    private int $_id;
    private string $_name;
    private string $_description;

    /**
     * Permission constructor.
     * @param int $id
     * @param string $name
     * @param string $description
     */
    public function __construct(int $id, string $name, string $description)
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_description = $description;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->_description;
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
        ];
    }
}