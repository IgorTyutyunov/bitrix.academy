<?
include_once ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
CModule::IncludeModule('iblock');
// объект инфоблока
$iblockId = 2;
$iblock = \Bitrix\Iblock\Iblock::wakeUp($iblockId);
$hlEntity = \Bitrix\Main\ORM\Entity::compileEntity(
    'BrandReference',
    [
        (new \Bitrix\Main\Entity\IntegerField('ID'))->configurePrimary(),
        (new \Bitrix\Main\ORM\Fields\StringField('UF_NAME'))->configureTitle('Название'),
        (new \Bitrix\Main\ORM\Fields\StringField('UF_XML_ID'))->configureTitle('Внешний код'),
    ],
    [
        'namespace' => 'BrandReference', 'table_name' => 'eshop_brand_reference'
    ]
);
//
//$propentity = \Bitrix\Main\ORM\Entity::compileEntity(
//    'BrandReference',
//    [
//        (new \Bitrix\Main\Entity\IntegerField('ID'))->configurePrimary(),
//        (new \Bitrix\Main\ORM\Fields\StringField('UF_NAME'))->configureTitle('Название'),
//        (new \Bitrix\Main\ORM\Fields\StringField('UF_XML_ID'))->configureTitle('Внешний код'),
//    ],
//    [
//        'namespace' => 'BrandReference', 'table_name' => 'eshop_brand_reference'
//    ]
//);

$elements = $iblock->getEntityDataClass()::getList([
    'select' => ['ID', 'NAME', 'CODE', 'NEWPRODUCT', 'BRND_XML_ID'=>'BRAND_REF.VALUE', 'BRAND_NAME'=>'BRAND.UF_NAME'],
    'runtime'=>[
        (new \Bitrix\Main\ORM\Fields\Relations\Reference('BRAND', $hlEntity, \Bitrix\Main\ORM\Query\Join::on('this.BRND_XML_ID', 'ref.UF_XML_ID')))->configureJoinType('left')
    //    (new \Bitrix\Main\ORM\Fields\Relations\Reference('NEWPRODUCT_NAME', $hlEntity, \Bitrix\Main\ORM\Query\Join::on('this.BRND_XML_ID', 'ref.UF_XML_ID')))->configureJoinType('left')
    ]

]);
while($element = $elements->fetch())
{
    pre($element);
}
include_once ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");?>
