<?php

namespace Academy\D7\DB\MySql;

use Bitrix\Main\Loader;
use \Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;

class BookTable extends DataManager
{

    const TABLE_NAME = 'book_d7';

    public static function getTableName()
    {
        return self::TABLE_NAME;
    }

    public static function getMap()
    {
        Loader::includeModule('iblock');
        return [
            /**
             * Поля таблицы
             */
            new Fields\IntegerField('ID', ['primary' => true, 'autocomplete' => true]),
            new Fields\StringField('NAME', ['required' => true]),
            new Fields\StringField('RELEASED', ['required' => true]),
            new Fields\StringField('ISBN', [
                'required' => true,
                'column_name' => 'ISBNCODE',
                'validation' => function()//валидация поля перед добавлением/обновлением
                {
                    return [new \Bitrix\Main\Entity\Validator\Unique];//проверка на уникальное значение
                   // return [new \Bitrix\Main\Entity\Validator\RegExp()];//проверка по решульрному выражению
                   // return [new \Bitrix\Main\Entity\Validator\Length()];//проверка на максимальную/минимальную длину строки
                   // return [new \Bitrix\Main\Entity\Validator\Range()];//проверка на максимальное/минимальное значение числа
                   //есть ещё ряд валидаторов предусмотренные битриксом, но можно и свои писать.
                }
            ]),
            new Fields\IntegerField('AUTHOR_ID'),
            new Fields\StringField('DESCRIPTION'),
            new Fields\DatetimeField('TIME_ARRIVAL', ['default_value' => new \Bitrix\Main\Type\DateTime]),

            /**
             * Вычисляемые поля, которые  физически не сущействует в таблице, но доступны в GetList.
             * Чтобы это поле было выведено, оно обязательно должно быть задано в select, например так: select=>['*', 'AGE_YEAR']
             */
            new Fields\ExpressionField('AGE_YEAR', 'YEAR(%s)', ['TIME_ARRIVAL']),

            /**
             * Создангие поля, связанного с другой ORM сущностью.
             * Пример GetList(): $entityBookTable = Academy\D7\DB\MySql\BookTable::GetList(['select' => ['AGE_YEAR','AUTHOR-'=>'AUTHOR', '*']]);
             */
            new Fields\Relations\Reference('AUTHOR', '\Bitrix\Iblock\ElementTable', ['=this.AUTHOR_ID' => 'ref.ID'])
        ];
    }

    /**
     * Обработчик события перед добавлением записи в таблицу. Неправальный вариант.
     */
    public static function EventHandlerBeforeAdd(\Bitrix\Main\ORM\Event $event)
    {
        /**
         * ЫВариант 1 как можно изменить данные перед изменением
         */
        //$obj = $event->getParameter('object');
        //$obj->set('UF_NAME', 'УРАcccАdddАddАА');

        /**
         * ЫВариант 2 как можно изменить данные перед изменением
         */
        //$result = new \Bitrix\Main\Entity\EventResult();
        //$result->modifyFields(['UF_NAME'=>'УХ ПУШКА']);
        //return $result;
    }

    /**
     * правильный способ обработки событий ORM. Аналогичные методы можно объявить для остальных событий ORM
     * @param \Bitrix\Main\ORM\Event $event
     */
    public static function onBeforeAdd(\Bitrix\Main\ORM\Event $event)
    {
        /**
         * Вариант 1 как можно изменить данные перед изменением
         */
        //$obj = $event->getParameter('object');
        //$obj->set('NAME', 'УРАcccАdddАddАА');

        /**
         * Вариант 2 как можно изменить данные перед изменением
         */
        //$result = new \Bitrix\Main\Entity\EventResult();
        //$result->modifyFields(['UF_NAME'=>'УХ ПУШКА']);
        //return $result;

        /**
         * Как можно добавить ошибку. Вывести ошибки можно $result->getErrorMessages().
         * $result возвращает метод Add, если добавлеине проиходит с помощью этого метода ORM.
         * Или $result возвращает $event->getParameter('object')->save() если работать с ORM как с сущностью.
         */
        //$result = new \Bitrix\Main\ORM\EventResult();
        //$result->addError(new Fields\FieldError($event->getEntity()->getField('NAME'), 'Какой-то текст ошибки, фатал эрроро'));
        //return $result;
    }
}