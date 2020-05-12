<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Service;

use Vulpix\Engine\RBAC\DataStructures\Collections\PermissionsCollection;
use Vulpix\Engine\RBAC\DataStructures\Collections\RolesCollection;

class PermissionVerificator
{
    public static function verify(RolesCollection $roles, string $permission) : bool {
        foreach ($roles as $role){
            /**
             * @var PermissionsCollection
             */
            if (($role->getPermissions())->offsetExists($permission)){
                return true;
            }
        }
        return false;
    }
}