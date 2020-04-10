<?php

use DI\ContainerBuilder;

require "configs/dependencies.php";

$builder = new ContainerBuilder();
$builder->addDefinitions($dependencies);
$container = $builder->build();

return $container;
