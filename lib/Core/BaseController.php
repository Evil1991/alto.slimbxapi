<?php

namespace Alto\Slimbxapi\Core;

use Psr\Container\ContainerInterface;

/**
 * Базовый абстрактный класс для контроллеров
 */
abstract class BaseController
{
    /**
     * Атрибут запроса: токен в исходном виде
     */
    const JWT_TOKEN_RAW_ATTRIBUTE_NAME = 'raw_token';

    /**
     * @var ContainerInterface $container
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

}
