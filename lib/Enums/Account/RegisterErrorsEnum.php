<?php

namespace Alto\Slimbxapi\Enums\Account;

class RegisterErrorsEnum
{
    const EMAIL_EXISTS = [
        'code'      => 'email_exists',
        'message'   => 'Почта уже занята.'
    ];

    const PHONE_EXISTS = [
        'code'      => 'phone_exists',
        'message'   => 'Телефон уже занят.'
    ];

    const EMPTY_FIO = [
        'code'      => 'empty_fio',
        'message'   => 'ФИО не заполнены.'
    ];

    const FIO_TOO_LONG = [
        'code'      => 'fio_too_long',
        'message'   => 'ФИО слишком длинные.'
    ];

    const FIO_HAS_UNALLOWED_SYMBOLS = [
        'code'      => 'fio_has_unallowed_symbols',
        'message'   => 'ФИО содержит спец. символы или цифры.'
    ];

    const EMPTY_REQIRED_FIELDS = [
        'code'      => 'empty_required_fields',
        'message'   => 'Не заполнены обязательные поля.'
    ];

    const WEAK_PASSWORD = [
        'code'      => 'weak_password',
        'message'   => 'Пароль не соответствует парольной политике.'
    ];

    const INVALID_EMAIL = [
        'code'      => 'invalid_email',
        'message'   => 'Неправильный формат email.'
    ];
    
    const ERROR_NEW_TEL = [
        'code'      => 'error_new_tel',
        'message'   => 'Произошла ошибка при запросе к new-tel'
    ];
    const ERROR_WHATSAPP = [
        'code'      => 'error_whats_app',
        'message'   => 'Произошла ошибка при запросе к WhatsApp'
    ];
    
    const EMPTY_PHONE = [
        'code'      => 'empty_phone',
        'message'   => 'Не передан параметр phone'
    ];

    const EMPTY_PASSWORD = [
        'code'      => 'empty_password',
        'message'   => 'Не передан параметр password'
    ];

    const PHONE_INVALID = [
        'code'      => 'phone_invalid',
        'message'   => 'Номер телефона задан в неверном формате'
    ];
    
    const ERROR_CONFIRM_CODE = [
        'code'      => 'error_confirm_code',
        'message'   => 'Введен не верный проверочный код'
    ];

    const ERROR_REGISTRATION = [
        'code'      => 'error_registration',
        'message'   => 'Ошибка при создании пользователя'
    ];
    
    const EMPTY_CONFIRM_CODE = [
        'code'      => 'empty_confirm_code',
        'message'   => 'Не передан параметр confirmCode'
    ];

    const ERROR_UPD_USER = [
        'code'      => 'error_upd_user',
        'message'   => 'Ошибка при обновлении пользователя'
    ];

    const USER_ALREADY_EXISTS = [
        'code'      => 'user_already_exists',
        'message'   => 'Пользователь уже существует'
    ];
}
