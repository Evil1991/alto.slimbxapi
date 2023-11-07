<?php

namespace Alto\Slimbxapi\Routes;

use \Slim\Handlers\Strategies\RequestResponseArgs;
use \Slim\Routing\RouteCollectorProxy;
use Alto\Slimbxapi\Middlewares\ApiAuthMiddleware;


class Main
{
    const AVAILABLE_API_VERSIONS = [
        '1',
    ];

    public static function init(&$app)
    {
        foreach (self::AVAILABLE_API_VERSIONS as $version) {

            $table = 'v_' . $version . '_routes_table.php';
            $routes = require $table;

            $prefix = 'v' . $version . '/';
            $app->group($prefix, function (RouteCollectorProxy $group) use ($routes) {
                foreach ($routes as $route) {
                    $method = $route['method'];
                    $middlewares = self::resolveMiddlewaresArray($route);

                    $gr = $group->$method($route['path'], [$route['class'], $route['function']]);

                    if ($middlewares !== []) {
                        foreach ($middlewares as $mf) {
                            $gr->add(self::$mf());
                        }
                    }

                    if (self::checkIsNeedParseArgs($route)) {
                        $gr->setInvocationStrategy(new RequestResponseArgs());
                    }

                    unset($gr);
                }
            });
        }
    }

    public static function resolveMiddlewaresArray($route): array
    {
        if (!array_key_exists('middlewares', $route)) {
            return [];
        }

        if (!is_array($route['middlewares'])) {
            return [];
        }

        return $route['middlewares'];
    }

    /**
     * Проверяем указано-ли явно необходимость парсинга аргументов в роутах
     * типа /user/{id} => getUser(Request $request, Response $response, $id)
     * Необходимость зазается с помощью ключа массива 'parseAgrs' => true
     * в файле с роутами
     */
    public static function checkIsNeedParseArgs($route): bool
    {
        if (!array_key_exists('parseAgrs', $route)) {
            return false;
        }

        return (bool)$route['parseAgrs'];
    }

    /**
     * Обертка для инициализации Middleware проверки JWT-авторизации
     */
    public static function initApiAuthMiddleware(): ApiAuthMiddleware
    {
        return new ApiAuthMiddleware;
    }
}
