<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\Database\Connectors\IConnector;
use Vulpix\Engine\RBAC\Domains\Permissions\PermissionManager;
use Vulpix\Engine\RBAC\Domains\Roles\RoleManager;

/**
 * На текущий момент Legacy class. ВОзможно в будующем понадобится для разделения логики на две части:
 * 1) Инициализация ролей
 * 2) Какие-то действия
 * 3) Проверка прав доступа
 *
 * Class InitRolesMiddleware
 * @package Vulpix\Engine\RBAC\Middleware
 */
class InitRolesMiddleware implements MiddlewareInterface
{
    private RoleManager $_roleManager;
    private PermissionManager $_permissionManager;

    /**
     * InitRolesMiddleware constructor.
     * @param IConnector $dbConnector
     * @param RoleManager $manager
     */
    public function __construct(RoleManager $roleManager, PermissionManager $permissionManager)
    {
        $this->_roleManager = $roleManager;
        $this->_permissionManager = $permissionManager;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $userId = $request->getAttribute('User')['userId'];

        /**
         * Кароче план.
         * RBAC это модуль, Vulpix это фреймворк.
         * Модуль не должен быть обязательным.
         * Модуль включается двумя мидлварами:
         * 1) InitRoles - инициализирует роли.
         * 2) VerifyPermission - проверяет разрешено ли текущему пользователю запускать котроллер.
         * Для реализации подобного можно создать файл <name> в конфигах.
         * Теперь если кто то хочет использовать модуль RBAC, в настройках pipeline нужно включить оба мидлвара.
         * А так же заполнить файл настроек по типу:
         * [
         *      \Vulpix\Engine\AAIS\Actions\AccountCreateAction::class => AAIS_ACCOUNT_CREATE,
         *      ......................................................................... etc
         * ]
         * Первым идет имя контроллера, вторым наименование првиелегии из БД.
         * VerifyPermissionMiddleware будет теперь проверять есть ли соответсвие запускаемого контроллера и правила из БД.
         * Если нету такого соответсвия значит котроллер обрабатывает операции свободные для всех.
         * Если соответсвие найдено (имя контролллера это же ключ массива) то првоерить наличии права доступа у пользователя
         * к запуску этого контроллера.
         * Такая реализация поможет:
         * 1) Не прописывать в каждый прям котроллер правила доступа (будут существовать общедоступные)
         * 2) Для закрытых котроллеров будет идти валидация.
         *
         * Файл таких настроек будет подгружаться в VerifyPermissionMiddleware.
         * Помимо файла, можно сделать например Класс который будет конфигурировать это все дело. !?
         *
         * Может быть так, что контроллеры являются закрытыми типа котроллеров RBAC модуля.
         * Но использование RBAC нет нужды в проекте! Тогда достаточно просто не включать Init Roles и VerifyPermission
         * мидлвары.
         *
         * К абзацу выше: закрытость котроллера определяется такой строкой в файле конфигурации rbac^
         * \Vulpix\Engine\AAIS\Actions\AccountCreateAction::class => AAIS_ACCOUNT_CREATE
         * Если такой строки нет, считается что контроллер открыт для всех пользователей, а значит все равно
         * активен ли модуль RBAC (включены Init Roles и VerifyPermission мидлавары) или нет.
         *
         */
        $collection = $this->_roleManager->getByUserId($userId);
        /**
         * Проинициализирую роли привелегиями
         */
        foreach ($collection as $key => $role){
            $permissions = $this->_permissionManager->initPermissions($role->getId());
            $role->setPermissions($permissions);
        }
        $request = $request->withAttribute('Roles', $collection);
        return $response = $handler->handle($request);
    }
}