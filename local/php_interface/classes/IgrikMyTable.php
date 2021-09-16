<?php

use Bitrix\Main\Entity;
class IgrikMyTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'igrik_hl';
    }

    public static function getMap()
    {
        return  [
            'ID' => (new \Bitrix\Main\ORM\Fields\IntegerField('ID'))->configurePrimary()->configureAutocomplete()->configureTitle('ID'),
            'UF_NAME' => (new \Bitrix\Main\ORM\Fields\StringField('UF_NAME'))->configureTitle('Наxзвание'),
            'UF_CODE' =>   (new \Bitrix\Main\ORM\Fields\StringField('UF_CODE'))->configureTitle('Символьный код'),
            'UF_DESCRIPTION' => (new \Bitrix\Main\ORM\Fields\StringField('UF_DESCRIPTION'))->configureTitle('Описание'),
            'UF_XML_ID' =>  (new \Bitrix\Main\ORM\Fields\StringField('UF_XML_ID'))->configureTitle('Внешний код'),
            'UF_FULL_DESCRIPTION' => (new \Bitrix\Main\ORM\Fields\StringField('UF_FULL_DESCRIPTION'))->configureTitle('Полное Описание'),
        ];
    }
}