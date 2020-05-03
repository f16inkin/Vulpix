<?php


namespace Vulpix\Engine\Core\Utility\ExceptionsHandler;


use Laminas\Diactoros\Response\JsonResponse;

/**
 * Базовый класс для всех ExceptionsHandler принадлежащих другим модулям.
 * Реализованны обработки PDO исключений.
 *
 * Class ExceptionsHandler
 * @package Vulpix\Engine\Core\Utility\ExceptionsHandler
 */
abstract class ExceptionsHandler
{
    /**
     * @return JsonResponse
     */
    protected function unhandled() : JsonResponse {
        return new JsonResponse('Данное исключение пока не обработано',500);
    }

    /**
     * @param \PDOException $e
     * @return JsonResponse
     */
    protected function handle_1062(\PDOException $e) : JsonResponse{
        return (new JsonResponse($e->getMessage(),500));
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
        return $this->$subMethod();
    }

    abstract public function handle(\Exception $e);

}