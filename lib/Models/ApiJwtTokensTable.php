<?php

namespace Alto\Slimbxapi\Models;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Fields;

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
            (new Fields\IntegerField("ID"))
                ->configurePrimary(true)
                ->configureAutocomplete(),
            (new Fields\IntegerField("B_USER_ID")),
            (new Fields\StringField("ACCESS_TOKEN")),
            (new Fields\StringField("REFRESH_TOKEN")),
        ];

        return $fieldsMap;
    }
}
