<?php

namespace Alto\Slimbxapi\Core\Dto;

class AuthDto
{
    /**
     * Поле, по которому производится авторизация
     */
    public $fieldLogin = 'LOGIN';

    /**
     * Значение передаваемого логина авторизации
     */
    public $valueLogin = '';

    /**
     * Поле где хранится пароль
     */
    public $fieldPassword = 'PASSWORD';

    /**
     * Значение передаваемого логина авторизации
     */
    public $valuePassword = '';

    /**
     * В случае успешной авторизации вернуть JWT токен
     */
    public $returnJWT = true;

    /**
     * Опция запомнить авторизацию (игнорируется в варианте JWT)
     */
    public $remember = false;
}