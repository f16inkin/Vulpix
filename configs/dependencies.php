<?php

return $dependencies = [
    \Vulpix\Engine\Database\Connectors\IConnector::class => DI\create(\Vulpix\Engine\Database\Connectors\MySQLConnector::class),
    \Vulpix\Engine\AAIS\Domains\Accounts\IAccountDataProvider::class => DI\create(\Vulpix\Engine\AAIS\Domains\Accounts\AccountMySQLDataProvider::class)->constructor(DI\get(\Vulpix\Engine\Database\Connectors\IConnector::class)),
    \Vulpix\Engine\RBAC\Domains\Roles\IRolesDataProvider::class => DI\create(\Vulpix\Engine\RBAC\Domains\Roles\RolesMySQLDataProvider::class)->constructor(DI\get(\Vulpix\Engine\Database\Connectors\IConnector::class))
];