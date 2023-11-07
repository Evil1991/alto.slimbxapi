<?php

namespace Alto\Slimbxapi\Core\Traits;

use Bitrix\Main\UserTable;
use Alto\Slimbxapi\ApiJwtTokensTable;
use Alto\Slimbxapi\Core\JwtTokenProcessor;
use Alto\Slimbxapi\Core\ResponseErrors;
use Alto\Slimbxapi\Core\Dto\AuthDto;
use Alto\Slimbxapi\Enums\Account\AuthErrorsEnum;
use Alto\Slimbxapi\Exceptions\LogicException;

trait AuthTrait
{
    /**
     * Авторизация по логину и паролю с возвратом JWT-токена
     *
     * @throws LogicException
     */
    public static function signinByLoginAndPassword(string $login, string $password, bool $needJWTToken = true, bool $rememberUser = false): array
    {
        $result = array();

        global $USER;

        // Проверка на существование/блокировку пользователя
        $user = UserTable::getList([
            'select' => [
                'ID',
            ],
            'filter' => [
                'LOGIN' => $login
            ]
        ])->fetch();

        if (!$user) {
            ResponseErrors::setError(
                AuthErrorsEnum::ERROR_AUTH['code'],
                AuthErrorsEnum::ERROR_AUTH['message']
            );

            throw new LogicException(ResponseErrors::getErrors());
        }

        if ((isset($USER) && $USER instanceof CUser) && $USER->IsAuthorized()) {
            $USER->Logout();
        } else {
            unset($_SESSION['SALE_USER_ID']);
        }

        $auth = $USER->Login($login, $password, $rememberUser ? "Y" : "N");

        if ($auth['TYPE'] != "ERROR") {
            if ($needJWTToken) {
                $result = self::generateJWTTokens((int)$user['ID']);
            } else {
                $result['STATUS'] = true;
            }
        } else {
            ResponseErrors::setError(
                AuthErrorsEnum::ERROR_AUTH['code'],
                AuthErrorsEnum::ERROR_AUTH['message']
            );

            throw new LogicException(ResponseErrors::getErrors());
        }

        return $result;
    }

    public static function authByField(AuthDto $data)
    {
        if ($data->fieldLogin === 'LOGIN') {
            return self::signinByLoginAndPassword(
                (string)$data->valueLogin,
                (string)$data->valuePassword,
                (bool)$data->returnJWT,
                (bool)$data->remember
            );
        }

        $result = [];

        global $USER;

        // Проверка на существование/блокировку пользователя
        $user = UserTable::getList([
            'select' => ['*', 'UF_*'],
            'filter' => [
                $data->fieldLogin => $data->valueLogin
            ]
        ])->fetch();

        if (!$user) {
            ResponseErrors::setError(
                AuthErrorsEnum::ERROR_AUTH['code'],
                AuthErrorsEnum::ERROR_AUTH['message']
            );

            throw new LogicException(ResponseErrors::getErrors());
        }

        $passwHash = $user[$data->fieldPassword];

        // Если пароль не был установлен нужно ошибку выдать
        // но это большой вопрос какую -- пока предлагаю общую
        if (empty($passwHash)) {
            ResponseErrors::setError(
                AuthErrorsEnum::GENERAL_AUTH_ERROR['code'],
                AuthErrorsEnum::GENERAL_AUTH_ERROR['message']
            );

            throw new LogicException(ResponseErrors::getErrors());
        }

        if (!password_verify($data->valuePassword, $passwHash)) {
            ResponseErrors::setError(
                AuthErrorsEnum::ERROR_AUTH['code'],
                AuthErrorsEnum::ERROR_AUTH['message']
            );

            throw new LogicException(ResponseErrors::getErrors());
        }

        // На случай если была прежняя авторизация
        if ((isset($USER) && $USER instanceof \CUser) && $USER->IsAuthorized()) {
            $USER->Logout();
        } else {
            unset($_SESSION['SALE_USER_ID']);
        }

        // Имитируем битриксовую авторизацию чтобы иметь возможность
        // использовать системные штуки битрикс вроде событий
        if (!$USER->Authorize($user['ID'], $data->remember)) {
            ResponseErrors::setError(
                AuthErrorsEnum::ERROR_AUTH['code'],
                AuthErrorsEnum::ERROR_AUTH['message']
            );

            throw new LogicException(ResponseErrors::getErrors());
        }

        if (!$data->returnJWT) {
            $result['STATUS'] = true;
            return $result;
        }

        return self::generateJWTTokens((int)$user['ID']);
    }

    /**
     * Генерация JWT-токенов для пользователя
     * @throws LogicException
     */
    protected static function generateJWTTokens(int $userID): array
    {
        $accessToken = (new JwtTokenProcessor)->createUserToken($userID);
        $refreshToken = md5((time() + rand(999, 99999)));

        if (empty($accessToken)) {
            throw new \Exception('Внутренняя ошибка создания JWT токена.');
        }

        ApiJwtTokensTable::add([
            'B_USER_ID'   => $userID,
            'ACCESS_TOKEN'      => $accessToken,
            'REFRESH_TOKEN'     => $refreshToken
        ]);

        return [
            'accessToken'  => $accessToken,
            'refreshToken' => $refreshToken
        ];
    }

    /**
     * Обновление пары access/refresh токен
     * @throws LogicException
     */
    public static function refreshJWTToken(string $accessToken, string $refreshToken)
    {
        $jwtToken = new JwtTokenProcessor;

        // Тут используется не fUser, а user
        // $fUserID = $jwtToken->getFUserIDFromToken($accessToken);

        $useID = $jwtToken->getUserIDFromToken($accessToken);

        $item = ApiJwtTokensTable::getList([
            'filter' => [
                'B_USER_ID'   => $useID,
                'REFRESH_TOKEN'     => $refreshToken
            ]
        ])->fetch();

        if (!$item) {
            throw new LogicException([AuthErrorsEnum::INVALID_REFRESH_TOKEN]);
        }

        // Поскольку в методе getFUserIDFromToken подпись и прочее НЕ ПРОВЕРЯЕТСЯ
        // самое простое это провести обратную проверку полученный из БД
        // access_token должен быть равным переданному access_token-у
        if ($item['ACCESS_TOKEN'] !== $accessToken) {
            throw new LogicException([AuthErrorsEnum::JWT_AUTH_TOKEN_INVALID], 401);
        }

        $accessToken = $jwtToken->createUserToken($useID);
        $refreshToken = md5((time() + rand(999, 99999)));

        if (empty($accessToken)) {
            throw new \Exception('Внутренняя ошибка создания JWT токена.');
        }

        ApiJwtTokensTable::update(
            $item['ID'],
            [
                'ACCESS_TOKEN'      => $accessToken,
                'REFRESH_TOKEN'     => $refreshToken
            ]
        );

        return [
            'accessToken'  => $accessToken,
            'refreshToken' => $refreshToken
        ];
    }

    /**
     * Серверное разлогирование пользователя.
     * Если передан третий аргумент как true
     * тогда удалит вообще все токены данного пользователя
     */
    public static function signout($userID, $token, $all = false): void
    {
        if ($all) {
            $tQuery = ApiJwtTokensTable::getList([
                'select'    => [
                    'ID'
                ],
                'filter'    => [
                    'B_USER_ID'   => $userID
                ]
            ]);
        } else {
            $tQuery = ApiJwtTokensTable::getList([
                'select'    => [
                    'ID'
                ],
                'filter'    => [
                    'B_USER_ID'   => $userID,
                    'ACCESS_TOKEN'      => $token
                ]
            ]);
        }

        while ($item = $tQuery->fetch()) {
            ApiJwtTokensTable::delete($item['ID']);
        }
    }

    /**
     * Метод проверки чтобы пользователь существовал и был активен
     * Используется мидлвейром авторизации
     */
    public static function checkIsValidUser(int $user_id): array
    {
        \Bitrix\Main\Loader::includeModule("sale");

        $result = [
            'status'    => false,
            'userID'    => null
        ];

        $user = UserTable::getList([
            'select' => [
                'ID',
                'ACTIVE'
            ],
            'filter' => [
                'ID' => $user_id
            ]
        ])->fetch();

        if (!empty($user) && $user['ACTIVE'] == 'Y') {
            $result['status'] = true;
            $result['userID'] = (int)$user['ID'];
        }

        return $result;
    }
}