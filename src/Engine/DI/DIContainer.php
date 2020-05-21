<?php

declare (strict_types = 1);

namespace Vulpix\Engine\DI;

use DI\Container;
use DI\ContainerBuilder;

/**
 * Декоратор.
 * Необходим если вдруг придется подгружать базовую конфигурацию зависимостей для фреймворка:
 * MySQLConnector, MySQLDataProviders и тд.
 *
 * Если фреймворк используется как package, то данные зависимости перекрываются по ключу новыми из папки
 * configs приложения если потребуется.
 *
 * Class DIContainer
 * @package Vulpix\Engine\DI
 */
class DIContainer extends ContainerBuilder
{
    public function __construct(string $containerClass = Container::class)
    {
        parent::__construct($containerClass);
        $dependencies = realpath(__DIR__ . '/../../../configs/permissions.php');
        $this->addDefinitions($dependencies);
        return $this;
    }

}