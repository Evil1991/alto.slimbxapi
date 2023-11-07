<?php

namespace Alto\Slimbxapi\Services;

use \CUser;
use Bitrix\Main\UserTable;
use Alto\Slimbxapi\Core\ResponseErrors;
use Alto\Slimbxapi\Core\Dto\AuthDto;
use Alto\Slimbxapi\Core\JwtTokenProcessor;
use Alto\Slimbxapi\Core\Traits\AuthTrait;
use Alto\Slimbxapi\Enums\Account\AuthErrorsEnum;
use Alto\Slimbxapi\Exceptions\LogicException;
use Alto\Slimbxapi\Helpers\InputStringHelper;
use Alto\Slimbxapi\Helpers\SmsHelper;

class AuthService
{
    /* Для авторизации по логину и паролю посмотри этот трейт -- там оно уже есть */
    use AuthTrait;

    /**
     * Пример ЭП (можно править на свое усмотрение)
     */
    public function someAuthEndpoint(string $pass, int $argTwo)
    {
        if (mb_strlen($pass) < 6) {
            ResponseErrors::setError(
                AuthErrorsEnum::PASSWORD_TOO_LOW['code'],
                AuthErrorsEnum::PASSWORD_TOO_LOW['message']
            );

            throw new LogicException(ResponseErrors::getErrors());
        }
    }
}
