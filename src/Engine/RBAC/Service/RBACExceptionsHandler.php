<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Service;

use Laminas\Diactoros\Response\JsonResponse;
use Vulpix\Engine\Core\Utility\ExceptionsHandler\ExceptionsHandler;

/**
 * Создан для обработки Exception бросаемых в модуле RBAC
 *
 * Class RBACExceptionsHandler
 * @package Vulpix\Engine\RBAC\Domains
 */
class RBACExceptionsHandler extends ExceptionsHandler
{
    /**
     * Подметод для обработки ошибок связанных с БД.
     * Может быть добавлено логирование. Пока что есть только вывод ошибки.
     *
     * @return JsonResponse
     */
    protected function handle_1062(\PDOException $e) : JsonResponse {
        return (new JsonResponse('Данная роль уже присутсвует в системе',500));
    }

    /**
     * @param \PDOException $e
     * @return JsonResponse
     */
    protected function handle_1064(\PDOException $e) : JsonResponse {
        return (new JsonResponse('На обработку переданы не верные аргументы',400));
    }

    /**
     * Фабричный метод для всех обработок ошибок связанных с PDO.
     *
     * @param \PDOException $pdoException
     * @return mixed
     */
    protected function handlePDOException(\PDOException $pdoException) {
        $errorInfo = $pdoException->errorInfo;
        $subMethod = 'handle_'.$errorInfo[1];
        return $this->$subMethod($pdoException);
    }

    /**
     * Фабричный метод для обработки всевозможных исключений брошенных в модуле RBAC.
     *
     * @param \Exception $e
     * @return JsonResponse
     */
    public function handle(\Exception $e){
        $method = 'handle'.basename(get_class($e));
        if (method_exists($this, $method)){
            return $this->$method($e);
        }else{
            return $this->unhandled($e);
        }
    }

}
