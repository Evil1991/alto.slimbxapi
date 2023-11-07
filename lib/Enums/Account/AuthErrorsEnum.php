<?php

namespace Alto\Slimbxapi\Enums\Account;

class AuthErrorsEnum
{
    const EMPTY_EMAIL = [
        'code'      => 'empty_email',
        'message'   => 'Не передано или пустое поле email.'
    ];

    const INVALID_EMAIL = [
        'code'      => 'invalid_email',
        'message'   => 'Неправильный формат email.'
    ];

    const EMPTY_PASSWORD = [
        'code'      => 'empty_password',
        'message'   => 'Не передано или пустое поле password.'
    ];

    const EMPTY_PHONE = [
        'code'      => 'empty_phone',
        'message'   => 'Не передано или пустое поле phone.'
    ];

    const PHONE_INVALID = [
        'code'      => 'phone_invalid',
        'message'   => 'Номер телефона задан в неверном формате'
    ];

    const USER_NOT_FOUND = [
        'code'      => 'user_not_found',
        'message'   => 'Пользователь не существует'
    ];

    const PROFILE_NOT_EXISTS = [
        'code'      => 'profile_not_exists',
        'message'   => 'Профиль пользователя не существует.'
    ];

    const PHONE_NOT_EXISTS = [
        'code'      => 'phone_not_exists',
        'message'   => 'Пользователь с указанным телефоном не существует.'
    ];

    const INVALID_LOGIN_PASS = [
        'code'      => 'invalid_login_pass',
        'message'   => 'Неправильное имя пользователя и пароль.'
    ];

    const USER_BLOCKED = [
        'code'      => 'user_blocked',
        'message'   => 'Пользователь заблокирован.'
    ];

    const FUSER_NOT_EXISTS = [
        'code'      => 'f_user_not_exists',
        'message'   => 'Корзина пользователя не существует.'
    ];

    const TOKEN_USER_NOT_VALID = [
        'code'      => 'token_user_not_valid',
        'message'   => 'Пользователь токена не существует или заблокирован.'
    ];

    const JWT_AUTH_HEADER_NOT_SENT = [
        'code'      => 'jwt_auth_header_not_sent',
        'message'   => 'Не передан или пустой заголовок Authorization.'
    ];

    const JWT_AUTH_HEADER_IS_NOT_BEARER = [
        'code'      => 'jwt_auth_heade_is_not_bearer',
        'message'   => 'Неправильный формат заголовка Authorization. Ожидается "Authorization: Bearer ***..."'
    ];

    const JWT_AUTH_TOKEN_EXPIRED = [
        'code'      => 'jwt_auth_token_expired',
        'message'   => 'Токен просрочен.'
    ];

    const JWT_AUTH_TOKEN_INVALID = [
        'code'      => 'jwt_auth_invalid',
        'message'   => 'Невалидный JWT токен.'
    ];

    const JWT_REFRESH_TOKEN_NOT_SENT = [
        'code'      => 'jwt_refresh_token_not_sent',
        'message'   => 'Не передан refresh токен.'
    ];

    const JWT_ACCESS_TOKEN_NOT_SENT = [
        'code'      => 'jwt_access_token_not_sent',
        'message'   => 'Не передан access токен.'
    ];

    const INVALID_REFRESH_TOKEN = [
        'code'      => 'invalid_refresh_token',
        'message'   => 'Не найдет refresh токен у данного пользователя.'
    ];

    const GENERAL_AUTH_ERROR = [
        'code'      => 'general_auth_error',
        'message'   => 'Общая ошибка авторизации.'
    ];

    const ERROR_AUTH = [
        'code'      => 'error_auth',
        'message'   => 'Не верный логин или пароль'
    ];

    const EMPTY_ACCESS_TOKEN = [
        'code'      => 'empty_access_token',
        'message'   => 'Не передано или пустое поле access_token'
    ];

    const EMPTY_REFRESH_TOKEN = [
        'code'      => 'empty_refresh_token',
        'message'   => 'Не передано или пустое поле refresh_token'
    ];

    const EMPTY_CONFIRM_CODE = [
        'code'      => 'empty_confirm_code',
        'message'   => 'Не передано или пустое поле код подтверждения.'
    ];

    const INVALID_CONFIRM_CODE = [
        'code'      => 'invalid_confirm_code',
        'message'   => 'Неправильный код подтверждения.'
    ];

    const EMPTY_PIN_CODE = [
        'code'      => 'empty_pin_code',
        'message'   => 'Не передано или пустое поле pin'
    ];

    const PASSWORD_TOO_LOW = [
        'code'      => 'password_too_low',
        'message'   => 'Минимальная длина: 6 символов.'
    ];

    const ERROR_UPDATE_PASS = [
        'code'      => 'error_update_pass',
        'message'   => 'Ошибка обновления доступа.'
    ];
}
