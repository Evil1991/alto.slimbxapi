<?php

namespace Alto\Slimbxapi\Core;

use \Exception;

/**
 * Класс сборщик ошибок. Ошибки скаладываются в формате ответа JSON.
 */
final class ResponseErrors
{
    private static $instance;
    /**
     * @var array $errors
     */
    protected $errors = [];

    private function __construct() { }

    /**
     * Сохранение ошибки
     *
     * @return void
     */
    public static function setError($code, $message)
    {
        $instance = self::getInstance();
        $instance->setErrorData($code, $message);
    }

    /**
     * Есть ошибки или нет
     */
    public static function hasErrors(): bool
    {
        $instance = self::getInstance();

        return (count($instance->getErrorsData()) > 0);
    }

    public static function getErrors(): array
    {
        return (self::getInstance())->getErrorsData();
    }

    public function setErrorData($code, $message)
    {
        $this->errors[] = [
            'code'      => $code,
            'message'   => $message,
        ];
    }

    public function getErrorsData(): array
    {
        return (array)$this->errors;
    }

    protected static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    private function __clone() { }
}
