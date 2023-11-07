<?php

namespace Alto\Slimbxapi\Core;

use \UnexpectedValueException;
use \Bitrix\Main\Web\JWT;
use Alto\Slimbxapi\Exceptions\LogicException;
use Alto\Slimbxapi\Enums\Account\AuthErrorsEnum;

class JwtTokenProcessor
{
    /**
     * Secret
     */
    private $secret;

    /**
     * Expiration
     */
    private $expiration;

    /**
     * Issuer
     */
    private $issuer;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (array_key_exists('API_JWT_SECRET', $_ENV)) {
            $this->secret = (string)$_ENV['API_JWT_SECRET'];
        } else {
            throw new \Exception('Не задано значение параметра API_JWT_SECRET в .env');
        }

        if (array_key_exists('API_JWT_EXPIRATION', $_ENV)) {
            $this->expiration = time() + (int)$_ENV['API_JWT_EXPIRATION'];
        } else {
            throw new \Exception('Не задано значение параметра API_JWT_EXPIRATION в .env');
        }

        if (array_key_exists('API_JWT_ISSUER', $_ENV)) {
            $this->issuer = (string)$_ENV['API_JWT_ISSUER'];
        } else {
            $this->issuer = 'localhost';
        }
    }

    /**
     * Возвращает JWT токен пользователя
     *
     * @return string
     */
    public function createFUserToken(int $fUserID)
    {
        $data = [
            'f_user_id' => $fUserID,
            'exp'       => $this->expiration,
            'iss'       => $this->issuer,
            'iat'       => time()
        ];

        return JWT::encode($data, $this->secret);
    }

    public function createUserToken(int $userID)
    {
        $data = [
            'user_id'   => $userID,
            'exp'       => $this->expiration,
            'iss'       => $this->issuer,
            'iat'       => time()
        ];

        return JWT::encode($data, $this->secret);
    }

    /**
     * Возвращает пэйлоад токена если он валидный и не просрочен
     *
     * @throws LogicException
     * @return object
     */
    public function readTokenIfValid(string $token)
    {
        try {
            return JWT::decode($token, $this->secret, ['HS256']);
        } catch(\Exception $e) {
            if ($e instanceof UnexpectedValueException) {
                // В принципе из всех ошибок стоит выделить только просроченный токен
                // чтобы приложению было проще ориентироваться
                if ($e->getMessage() === 'Expired token') {
                    throw new LogicException([AuthErrorsEnum::JWT_AUTH_TOKEN_EXPIRED], 401);
                }
            }
            // Остальные возможные ошибки смысла нет отдельно выделять
            throw new LogicException([AuthErrorsEnum::JWT_AUTH_TOKEN_INVALID], 401);
        }
    }

    /**
     * Получение FUserID из токена
     *
     * @throws LogicException
     */
    public function getFUserIDFromToken(string $token): int
    {
        // Поскольку встроенный класс JWT не позволяет (исключение будет)
        // декодить токен в случае если токен просрочен, по этому придется
        // это делать руками, по факту нужно достать f_user_id чтобы работа с БД
        // была эффективней чем искать по токену целиком и полностью
        $tks = explode('.', $token);
		if (count($tks) != 3) {
			throw new LogicException([AuthErrorsEnum::JWT_AUTH_TOKEN_INVALID], 401);
		}

		list($headb64, $bodyb64, $cryptob64) = $tks;

        if (null === $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64))) {
			throw new LogicException([AuthErrorsEnum::JWT_AUTH_TOKEN_INVALID], 401);
		}

        $fUserID = (int)$payload->f_user_id;

        if ($fUserID <= 0) {
            throw new LogicException([AuthErrorsEnum::JWT_AUTH_TOKEN_INVALID], 401);
        }

        return $fUserID;
    }

    /**
     * Получение UserID из токена
     *
     * @throws LogicException
     */
    public function getUserIDFromToken(string $token): int
    {
        // Поскольку встроенный класс JWT не позволяет (исключение будет)
        // декодить токен в случае если токен просрочен, по этому придется
        // это делать руками, по факту нужно достать f_user_id чтобы работа с БД
        // была эффективней чем искать по токену целиком и полностью
        $tks = explode('.', $token);
		if (count($tks) != 3) {
			throw new LogicException([AuthErrorsEnum::JWT_AUTH_TOKEN_INVALID], 401);
		}

		list($headb64, $bodyb64, $cryptob64) = $tks;

        if (null === $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64))) {
			throw new LogicException([AuthErrorsEnum::JWT_AUTH_TOKEN_INVALID], 401);
		}

        $userID = (int)$payload->user_id;

        if ($userID <= 0) {
            throw new LogicException([AuthErrorsEnum::JWT_AUTH_TOKEN_INVALID], 401);
        }

        return $userID;
    }
}
