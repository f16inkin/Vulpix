<?php

return $dependencies = [
    \Vulpix\Engine\Database\Connectors\IConnector::class => DI\create(\Vulpix\Engine\Database\Connectors\MySQLConnector::class),
];