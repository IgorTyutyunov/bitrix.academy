<?
function pre($ar){
    echo "<pre>";
    print_r($ar);
    echo "</pre>";
}

/**
 * Метод посто заменяет слешы / на \. Нужно если разрабатываешь на локальном сервере на винде
 */
function rls($path)
{
    return str_replace('/', '\\', $path);
}

function getH1()
{

    global $APPLICATION;
    $title = $APPLICATION->GetTitle();

    if($APPLICATION->GetPageProperty('is_add_h1') === 'Y')
    {
        $title .= ' в Москве';

    }
    return $title;
}

CModule::AddAutoloadClasses(
    '', // не указываем имя модуля
    array(
        // ключ - имя класса, значение - путь относительно корня сайта к файлу
        'IgrikMyTable'         => '/local/php_interface/classes/IgrikMyTable.php',
    )
);
include_once ($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/handlers/usertypeelements.php");
include_once ($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/handlers/usertypesection.php");

$eventManager = \Bitrix\Main\EventManager::getInstance();
//Bitrix\Main\GroupTable::
$eventManager->addEventHandler("",  "IgrikMyOnBeforeUpdate", "test");
function test(\Bitrix\Main\Event $event)
{

    /**
     * @var Bitrix\Main\ORM\Entity $entity
     */
    $entity = $event->getEntity();
   // pre($event->getParameters());
    $obj = $event->getParameter('object');
    $obj->set('UF_NAME', 'УРАcccАdddАddАА');
    //$obj->save();
//    $result = new \Bitrix\Main\Entity\EventResult();
//    $result->modifyFields(['UF_NAME'=>'УХ ПУШКА']);
//    return $result;
}
//$newOb = IgrikMyTable::getEntity();
//IgrikMyTable::update(1,['UF_NAME' => 'testss']);

$tes = IgrikMyTable::getList();
/**
 * @var \Bitrix\Main\ORM\Objectify\EntityObject $r
 */
while($r = $tes->fetchObject())
{
   // $r->set('UF_NAME', 'ХА-хА-хА');
    //$r->save();
}


//$eventManager->addEventHandler("main",  "\Bitrix\Main\Group::OnBeforeUpdate", "testGr");
//function testGr(\Bitrix\Main\Event $event)
//{
//   // pre([1,2,3]);
//}
//
//Bitrix\Main\GroupTable::update(12,
//    ['NAME' => 'tests', 'DESCRIPTION' => 'Тестовая группа']
//);