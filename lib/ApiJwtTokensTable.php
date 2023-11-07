<?php

namespace Alto\Slimbxapi;

use \Bitrix\Main\Entity\DataManager;

class ApiJwtTokensTable extends DataManager
{
    const TABLE_NAME = "api_jwt_tokens";

    public static function getTableName()
    {
        return self::TABLE_NAME;
    }

    public static function getMap()
    {
        $fieldsMap =[
            'ID' => [
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
            ],
            'B_USER_ID' => [
                'data_type' => 'integer',
            ],
            'ACCESS_TOKEN' => [
                'data_type' => 'string',
            ],
            'REFRESH_TOKEN' => [
                'data_type' => 'string',
            ],
        ];

        return $fieldsMap;
    }
}
