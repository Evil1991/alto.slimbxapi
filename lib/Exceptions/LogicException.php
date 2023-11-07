<?php

namespace Alto\Slimbxapi\Exceptions;

use \Exception;

class LogicException extends Exception
{
    protected $errorsPayload = [];

    public function __construct(array $errors, $code = 400)
    {
        $this->errorsPayload = $errors;
        parent::__construct('', $code);
    }

    /**
     * Возвращает массив ошибок
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errorsPayload;
    }
}
