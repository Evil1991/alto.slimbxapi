<?php

namespace Alto\Slimbxapi\Enums\Account;

class UserErrorsEnum
{
    const EMPTY_OLD_PASSWORD = [
        'code' => 'empty_old_password',
        'message' => 'Не передано или пустое поле oldPassword.'
    ];
    const INCORRECT_OLD_PASSWORD = [
        'code' => 'incorrect_old_password',
        'message' => 'Ввденный старый пароль не совпадает со старым паролем пользователя'
    ];
    const EMPTY_PASSWORD = [
        'code' => 'empty_password',
        'message' => 'Не передано или пустое поле password.'
    ];
    const EMPTY_PASSWORD_CONFIRMATION = [
        'code' => 'empty_password_confirmation',
        'message' => 'Не передано или пустое поле passwordConfirmation.'
    ];
    const INCORRECT_PHONE = [
        'code' => 'LOGIN',
        'message' => 'Введен неверный номер телефона'
    ];
    const INCORRECT_EMAIL = [
        'code' => 'EMAIL',
        'message' => 'Введен неверный Email'
    ];
    const SET_PUSH_ERROR = [
        'code' => 'set_push_error',
        'message' => 'Ошибка установки push-токена'
    ];
    const DELETE_ACCOUNT_ERROR = [
        'code' => 'delete_account_error',
        'message' => 'Ошибка удаления аккаунта'
    ];
}
