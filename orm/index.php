<?
include_once ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
//Создание сущности для работы с таблицей без создания класса
$hlEntity = \Bitrix\Main\ORM\Entity::compileEntity(
    'HLEntity',
    [
        (new \Bitrix\Main\Entity\IntegerField('ID'))->configurePrimary(),
        (new \Bitrix\Main\ORM\Fields\StringField('UF_NAME'))->configureTitle('Название'),
        (new \Bitrix\Main\ORM\Fields\StringField('UF_CODE'))->configureTitle('Симмвольый код'),
    ],
    [
        'namespace' => 'IgrikHL', 'table_name' => 'igrik_hl'
    ]
);
$requare = (new Bitrix\Main\ORM\Query\Query($hlEntity))->setSelect(['*'])->exec();
while($res = $requare->fetch())
{
    pre($res);
}die;

//Связывание полей - способ номер 2. Более грамотный, с добавлением поля в сущность,
// Такде туту реадизовано кеширование выборки и очистка кеша по названию определённой таблоицы.
//Важно помнить, что кеш не будет создан без подключения footer.php или /bitrix/modules/main/include/epilog_after.php
$elementsEntity = Bitrix\Iblock\ElementTable::getEntity();
$elementsEntity->cleanCache();
$fileEntity = \Bitrix\Main\FileTable::getEntity();
$elementsEntity->addField(
    (new \Bitrix\Main\ORM\Fields\Relations\Reference('PICTURE',
    $fileEntity,
    \Bitrix\Main\ORM\Query\Join::on('this.DETAIL_PICTURE', 'ref.ID')))
        ->configureJoinType('left'));

$quaryElements = (new Bitrix\Main\ORM\Query\Query($elementsEntity))
    ->setSelect(['ID', 'NAME', 'DETAIL_PICTURE',
        'PICTURE_SUBDIR' => 'PICTURE.SUBDIR',
        'PICTURE_FILE_NAME' => 'PICTURE.FILE_NAME',
        new \Bitrix\Main\Entity\ExpressionField(
            'PICTURE_PATH',
            'CONCAT("new/upload/", %s, "/", %s)',
            ['PICTURE.SUBDIR', 'PICTURE.FILE_NAME']
        )
    ])
    ->whereNotNull('DETAIL_PICTURE')
    ->setCacheTtl(100)
    ->cacheJoins(true)
    ->exec();
while($r = $quaryElements->fetch())
{
    pre($r);
}
die;

//Связывание полей - способ номер 1
$elementsEntity = Bitrix\Iblock\ElementTable::getEntity();
$fileEntity = \Bitrix\Main\FileTable::getEntity();
$quaryElements = (new Bitrix\Main\ORM\Query\Query($elementsEntity))
    ->setSelect(['ID', 'NAME', 'DETAIL_PICTURE',
        'PICTURE_SUBDIR' => 'PICTURE.SUBDIR',
        'PICTURE_FILE_NAME' => 'PICTURE.FILE_NAME',
        new \Bitrix\Main\Entity\ExpressionField(
            'PICTURE_PATH',
            'CONCAT("/upload/", %s, "/", %s)',
            ['PICTURE.SUBDIR', 'PICTURE.FILE_NAME']
        )
    ])
    ->whereNotNull('DETAIL_PICTURE')
    ->registerRuntimeField('PICTURE',
        (new \Bitrix\Main\ORM\Fields\Relations\Reference('PICTURE', $fileEntity, \Bitrix\Main\ORM\Query\Join::on('this.DETAIL_PICTURE', 'ref.ID')))->configureJoinType('left')
    )->exec();
while($r = $quaryElements->fetch())
{
    pre($r);
}

//Связывание полей - способ в ЛОБ - номер 0
$elementEntity = Bitrix\Iblock\ElementTable::getEntity();
$fileEntity = \Bitrix\Main\FileTable::getEntity();
$quaryElements = (new Bitrix\Main\ORM\Query\Query($elementEntity))->setFilter(['IBLOCK_ID' => 2])->setSelect(['ID', 'NAME', 'DETAIL_PICTURE'])->whereNotNull('DETAIL_PICTURE');
$resElements = $quaryElements->exec();
while ($res = $resElements->fetch())
{
    $quaryFiles = (new \Bitrix\Main\ORM\Query\Query($fileEntity))->setFilter(['ID' => $res['DETAIL_PICTURE']])->setSelect(['*'])->exec();
    pre($quaryFiles->fetch());
}

//Работа с хайлодом через ORM: выборка, изменение, добавлене записи
class IgrikTable extends \Bitrix\Main\ORM\Data\DataManager {
    public static function getTableName()
    {
        return 'igrik_hl';
    }
    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID',
            ),
            'UF_NAME' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => 'NAME',
            ),
            'UF_CODE' => array(
                'data_type' => 'string',
               // 'required' => true,
                'title' => 'NAME',
            ),
        );

        //Альтернативный вариант описания полей
        return  [
            'ID' => (new \Bitrix\Main\ORM\Fields\IntegerField('ID'))->configurePrimary()->configureAutocomplete()->configureTitle('ID'),
            'UF_NAME' => (new \Bitrix\Main\ORM\Fields\StringField('UF_NAME'))->configureTitle('Наxзвание'),
            'UF_CODE' =>   (new \Bitrix\Main\ORM\Fields\StringField('UF_CODE'))->configureTitle('Символьный код'),
            ];
    }
}

$e = IgrikTable::add([//Можно добавить новую запись вот так. Если в  GetMap у поля 'required' => true, то оно должно быть обязательно заполненным при добавлении записи
    'UF_NAME' => 'name',
    //'UF_CODE' => 'code',
]);

//А можно добавить новую запись вот так.
$newOb = IgrikTable::createObject();
$newOb->set('UF_NAME', 'Новая запись');
//$newOb->set('UF_CODE', 'Символьеый код');
$newOb->save();


IgrikTable::update(1, ['UF_NAME' => 'Жёлтая пресса']);//Вот так можно изенить запись в хайлоде
$res = IgrikTable::getList(['filter' => ['ID' =>1], 'select' =>['ID', 'UF_NAME']]);
/**
 * @var \Bitrix\Main\ORM\Objectify\EntityObject $r;
 */
while ($r = $res->fetchObject())//Вот так можно изиенять записи в хайлоде
{
    $r->set('UF_NAME', 'УХ ПУШКА');
    $r->save();
}

include_once ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/epilog_after.php");//Без подключения этого или footer.php кеш не будет создан.
include_once ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");?>
