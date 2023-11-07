<?php

namespace Alto\Slimbxapi\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Alto\Slimbxapi\Core\BaseController;
use Alto\Slimbxapi\Core\ResponseBuilder;

class TestController extends BaseController
{
    /**
     * Метод проверки работоспособности API
     */
    public function hello(Response $response): Response
    {
        $server = '';
        if ($this->container->get('isDevEnvironment') === true) {
            $server .= ' on dev server';
        }

        $message = 'It`s works' . $server;
        return ResponseBuilder::successJSON($response, ['result' => $message]);
    }

    public function getAuthUser(Response $response): Response
    {
        global $USER;

        $result = [
            'id'    => (int)$USER->GetID(),
            'name'  => (string)$USER->GetFirstName() . ' ' . (string)$USER->GetLastName()
        ];

        return ResponseBuilder::successJSON($response, $result);
    }
}
