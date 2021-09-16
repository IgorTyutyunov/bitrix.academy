<?php

namespace Module\Frame\DB\MySql;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\Type\DateTime;

class ModuleFrameTable extends DataManager
{
    const TABLE_NAME = "b_module_frame_table";

    public static function getTableName()
    {
        return self::TABLE_NAME;
    }

    public static function getMap()
    {
        $fieldsMap = array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
            ),
            'CODE' => array(
                'data_type' => 'string',
            ),
            'NAME' => array(
                'data_type' => 'string',
            ),
            'DESCRIPTION' => array(
                'data_type' => 'string',
            ),
            'DATE_INSERT' => array(
                'data_type' => 'datetime',
                "default_value" => new DateTime(),
            ),
        );
        return $fieldsMap;
    }
}
