<?php

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Vulpix\Engine\Core\Infrastructure\ApplicationFactory;

#Автозагрузка
chdir(dirname(__DIR__));
require "vendor/autoload.php";

#Инициализация
require "configs/container.php";

$app = ApplicationFactory::create($container);

#Загрузка Middleware
require "configs/pipeline.php";

require "configs/routes.php";

#Запуск приложения
$request = ServerRequestFactory::fromGlobals();
$response = $app->handle($request);

#Отправка клиенту
$emitter = new SapiEmitter();
$emitter->emit($response);





