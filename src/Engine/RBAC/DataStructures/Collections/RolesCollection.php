<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\DataStructures\Collections;

use ArrayIterator;
use InvalidArgumentException;
use Traversable;
use Vulpix\Engine\Core\DataStructures\Collections\ICollection;
use Vulpix\Engine\RBAC\DataStructures\Entity\Role;

/**
 * Коллекция. Включает в себя объекты класса Role.
 *
 * Class RolesCollection
 * @package Vulpix\Engine\RBAC\DataStructures\Collections
 */
class RolesCollection implements ICollection, \JsonSerializable
{
    private array $_roles = [];

    /**
     * Retrieve an external iterator
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->_roles);
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->_roles[$offset]) ? true : false;
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset) : ?Role
    {
        if (isset($this->_roles[$offset])) {
            return $this->_roles[$offset];
        }
        return null;
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value) : void
    {
        if ($value instanceof Role){
            $this->_roles[$offset] = $value;
        }else{
            throw new InvalidArgumentException('Передано значение отличное от класса Role');
        }
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset) : void
    {
        if (isset($this->_roles[$offset])) {
            unset($this->_roles[$offset]);
        }else{
            throw new InvalidArgumentException('Роль отсуствует в коллекции');
        }
    }

    /**
     * Count elements of an object
     * @link https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count() : int
    {
       return count($this->_roles);
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
        return $this->_roles;
    }
}