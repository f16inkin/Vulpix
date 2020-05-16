<?php

declare(strict_types = 1);

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
    protected function unhandled(\Exception $e) : JsonResponse {
        return new JsonResponse('Данное исключение пока не обработано '.$e->getMessage(),500);
    }

    protected function handleUnexpectedValueException(\Exception $e){
        $errorMessage = [];
        $errorMessage['header'] = 'Поймано исключение типа Unexpected Value Exception';
        $errorMessage['thrower'] = 'Класс бросивший исключение '.$e->getFile();
        $errorMessage['thrower_line'] = 'На линии '.$e->getLine();
        $errorMessage['initiator'] = 'Класс инициатор '.$e->getTrace()[0]['file'];
        $errorMessage['initiator_line'] = 'На линии '.$e->getTrace()[0]['line'];
        $errorMessage['code'] = $e->getCode();
        $errorMessage['message'] = $e->getMessage();
        return new JsonResponse($errorMessage);
    }

    protected function handleInvalidArgumentException(\Exception $e) : JsonResponse
    {
        $errorMessage = [];
        $errorMessage['header'] = 'Поймано исключение типа Invalid Argument Exception';
        $errorMessage['thrower'] = 'Класс бросивший исключение '.$e->getFile();
        $errorMessage['thrower_line'] = 'На линии '.$e->getLine();
        $errorMessage['initiator'] = 'Класс инициатор '.$e->getTrace()[0]['file'];
        $errorMessage['initiator_line'] = 'На линии '.$e->getTrace()[0]['line'];
        $errorMessage['code'] = $e->getCode();
        $errorMessage['message'] = $e->getMessage();
        return new JsonResponse($errorMessage);
    }

    protected function handle_1055(\PDOException $e) : JsonResponse {
        return (new JsonResponse($e->getMessage(),500));
    }

    protected function handle_1062(\PDOException $e) : JsonResponse {
        return (new JsonResponse($e->getMessage(),500));
    }

    protected function handle_1064(\PDOException $e) : JsonResponse {
        return (new JsonResponse($e->getMessage(),500));
    }

    protected function handle_1406(\PDOException $e) : JsonResponse {
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
        return $this->$subMethod($pdoException);
    }

    abstract public function handle(\Exception $e);

}