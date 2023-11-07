<?php

namespace Alto\Slimbxapi\Middlewares;

use \CUser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Alto\Slimbxapi\Core\BaseController;
use Alto\Slimbxapi\Core\JwtTokenProcessor;
use Alto\Slimbxapi\Enums\Account\AuthErrorsEnum;
use Alto\Slimbxapi\Exceptions\LogicException;
use Alto\Slimbxapi\Services\Auth;

class ApiAuthMiddleware implements MiddlewareInterface
{
    use \Alto\Slimbxapi\Core\Traits\AuthTrait;

    protected $options = [
        'header'    => 'Authorization',
        'regexp'    => '/Bearer\s+(.*)$/i',
    ];

    // ID корзины
    protected $fuserID = null;

    // ID пользователя
    protected $userID = null;

    protected $handledRequest = null;

    public function __construct(array $options = [])
    {
        if ($options !== []) {
            foreach ($options as $option => $value) {
                $this->options[$option] = $value;
            }
        }
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // По степени приоритета -- сначала пробуем авторизовать по токену
        // Если токен конечно же присутствует
        $JWTTokenAuthResult = $this->processJWT($request);

        // Пробуем авторизацию через стандартный механизм битрикса
        $bitrixAuthResult = false;
        if (!$JWTTokenAuthResult) {
            $bitrixAuthResult = $this->processBitrixAuth();
        }

        // 1. Если была успешная авторизация через механизм битрикса -- ничего не делаем
        if ($bitrixAuthResult) {
            return $handler->handle($request);
        }

        // 2. Если авторизация битриксом не удалась, НО JWT авторизация ок
        // тогда нужно принудительно авторизовать под пользователем в $this->userID
        if ($this->forceReloadGlobalUser()) {
            return $this->handledRequest != null ? $handler->handle($this->handledRequest) : $handler->handle($request);
        }

        // Если ничего из вышеприведенного не сработало -- не авторизован
        throw new LogicException([AuthErrorsEnum::GENERAL_AUTH_ERROR], 401);
    }

    public function processRawQuery(string $tokenString)
    {
        return $this->processRawJWT($tokenString);
    }

    /**
     * Попытка авторизации по токену (JWT)
     */
    private function processJWT(ServerRequestInterface $request): bool
    {
        $header = $request->getHeaderLine($this->options['header']);

        if (empty($header)) {
            return false;
        }

        $token = $this->getTokenFromHeaderLine($header);
        if (empty($token)) {
            $this->responseUnauthorized([
                AuthErrorsEnum::JWT_AUTH_HEADER_IS_NOT_BEARER
            ]);
        }

        $tokenData = (new JwtTokenProcessor)->readTokenIfValid($token);

        // реализация когда в токене f_user_id
        // $checkUserResult = Auth::checkIsValidFUser((int)$tokenData->f_user_id);

        $checkUserResult = self::checkIsValidUser((int)$tokenData->user_id);

        if (!$checkUserResult['status']) {
            $this->responseUnauthorized([
                AuthErrorsEnum::TOKEN_USER_NOT_VALID
            ]);
        } else {
            // для некоторых методов требуется сырой токен
            $this->handledRequest = $request->withAttribute(BaseController::JWT_TOKEN_RAW_ATTRIBUTE_NAME, $token);
            $this->userID = $checkUserResult['userID'];
        }

        return true;
    }

    /**
     * Возвращает ID пользователя из токена (после прохождения проверки)
     * FALSE в случае неудачи
     */
    private function processRawJWT(string $tokenString)
    {

        $token = $this->getTokenFromHeaderLine($tokenString);
        if (empty($token)) {
            return false;
        }

        try {
            $tokenData = (new JwtTokenProcessor)->readTokenIfValid($token);
        } catch (\Exception $e) {
            if ($e instanceof LogicException) {
                header('HTTP/1.0 401 Unauthorized');
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['status' => false, 'errors' => $e->getErrors()]);
                die();

            } else {
                return false;
            }
        }

        $checkUserResult = self::checkIsValidUser((int)$tokenData->user_id);

        if (!$checkUserResult['status']) {
            return false;
        }

        return (int)$checkUserResult['userID'];
    }

    /**
     * Битриксовая авторизация (через штатный механизм кукисов/сессий)
     */
    private function processBitrixAuth(): bool
    {
        global $USER;

        if ((isset($USER) && $USER instanceof CUser) && $USER->IsAuthorized()) {
            return true;
        }

        return false;
    }

    private function forceReloadGlobalUser(): bool
    {
        global $USER;

        if (!($USER instanceof CUser)) {
            $USER = new CUser;
        } elseif ($USER->IsAuthorized()) {
            $USER->Logout();
        }

        return (bool)$USER->Authorize($this->userID);
    }

    /**
     * 401-й ответ от сервера
     *
     * @throws LogicException
     */
    private function responseUnauthorized(array $errors)
    {
        throw new LogicException($errors, 401);
    }

    /**
     * Получение токена из заголовка
     */
    private function getTokenFromHeaderLine(string $line): string
    {
        return preg_match($this->options['regexp'], $line, $matches) ? (string)$matches[1] : '';
    }
}
