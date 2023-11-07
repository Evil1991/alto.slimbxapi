<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;
use Alto\Slimbxapi\Core\ApiErrorHandler;

require_once __DIR__ . './../local/vendor/autoload.php';
require_once __DIR__ . './../bitrix/modules/main/include/prolog_before.php';

\Bitrix\Main\Loader::includeModule('alto.slimbxapi');

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');
$isDevEnvironment = (
    array_key_exists('IS_DEV_ENVIRNOMENT', $_ENV)
    && ($_ENV['IS_DEV_ENVIRNOMENT'] === true || $_ENV['IS_DEV_ENVIRNOMENT'] === 'true'));

// Инициализация контейнера
$builder = new ContainerBuilder();
$displayErrorDetails = true;
if (!$isDevEnvironment) {
    // TODO: Настроить для prod пути
    $builder->enableCompilation(__DIR__ . '/tmp');
    $builder->writeProxiesToFile(true, __DIR__ . '/tmp/proxies');
    $displayErrorDetails = false;
}
$container = $builder->build();

$container->set('isDevEnvironment', $isDevEnvironment);

// Инициализация приложения
$app = Bridge::create($container);
$app->setBasePath('/api/');

// Инициализация роутов
\Alto\Slimbxapi\Routes\Main::init($app);

// Parse json, form data and xml
$app->addBodyParsingMiddleware();

// Перехват ошибок
$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();

// Обработка /user/ => /user оно же трейлинг слеш
$app->add(new \Alto\Slimbxapi\Middlewares\TrailingSlashMiddleware());

$errorHandler = new ApiErrorHandler($callableResolver, $responseFactory);
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

$app->run();

// В install модуля
// - slimbxapi.php
// - swagger с 2-мф ЭП
// - заголовок Authorization, пофиксить сваггер
// файлы композера в папку install/7.4
// пример ЧПУ для urlrewrite.php