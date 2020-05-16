<?php

return $dependencies = [
    \Vulpix\Engine\Database\Connectors\IConnector::class => DI\create(\Vulpix\Engine\Database\Connectors\MySQLConnector::class),
    \Vulpix\Engine\AAIS\Domains\Accounts\IAccountStorage::class => DI\create(\Vulpix\Engine\AAIS\Domains\Accounts\AccountMySQLStorage::class)->constructor(DI\get(\Vulpix\Engine\Database\Connectors\IConnector::class))
];