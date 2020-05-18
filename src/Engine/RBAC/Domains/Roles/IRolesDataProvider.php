<?php

declare (strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains\Roles;

interface IRolesDataProvider
{
    public function getById(int $id) : Role;

    public function getByName(string $name) : Role;

    public function getByUserId(int $userId) : RolesCollection;

    public function getPartly(int $start, int $offset) : RolesCollection;

    public function insert(Role $role) : int;

    public function update(Role $role) : void;

    public function delete(array $roleIDs) : void;

}