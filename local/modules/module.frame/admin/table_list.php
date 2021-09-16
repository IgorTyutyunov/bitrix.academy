<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); // первый общий пролог
$MODULE_ID = pathinfo(dirname(__DIR__))["basename"];
Bitrix\Main\Loader::includeModule($MODULE_ID);

global $APPLICATION;
// подключим языковой файл
IncludeModuleLangFile(__FILE__);

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight($MODULE_ID);
// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$sTableID = ModuleFrameTable::getTableName(); // ID таблицы
$oSort = new CAdminSorting($sTableID, "ID", "desc"); // объект сортировки
$lAdmin = new CAdminUiList($sTableID, $oSort); // основной объект списка

// проверку значений фильтра для удобства вынесем в отдельную функцию
function CheckFilter()
{
    global $FilterArr, $lAdmin;
    foreach ($FilterArr as $f) global $$f;

    /*
       здесь проверяем значения переменных $find_имя и, в случае возникновения ошибки,
       вызываем $lAdmin->AddFilterError("текст_ошибки").
    */

    return count($lAdmin->arFilterErrors) == 0; // если ошибки есть, вернем false;
}
// опишем элементы фильтра
$FilterArr = Array(
    "find_id",
    "find_code",
    "find_name",
    "find_description"
);

// инициализируем фильтр
$lAdmin->InitFilter($FilterArr);
// если все значения фильтра корректны, обработаем его
if (CheckFilter())
{
    // создадим массив фильтрации для выборки CRubric::GetList() на основе значений фильтра
    $arFilter = Array(
        "ID"      => $find_id,
        "CODE"    => $find_code,
        "NAME" => $find_name,
        "DESCRIPTION"  => $find_description,
    );
}

// сохранение отредактированных элементов
if($lAdmin->EditAction() && $POST_RIGHT=="W")
{
    // пройдем по списку переданных элементов
    foreach($FIELDS as $ID=>$arFields)
    {
        if(!$lAdmin->IsUpdated($ID))
            continue;

        // сохраним изменения каждого элемента
        $DB->StartTransaction();
        $ID = IntVal($ID);
        $cData = new CRubric;
        if(($rsData = $cData->GetByID($ID)) && ($arData = $rsData->Fetch()))
        {
            foreach($arFields as $key=>$value)
                $arData[$key]=$value;
            if(!$cData->Update($ID, $arData))
            {
                $lAdmin->AddGroupError(GetMessage("rub_save_error")." ".$cData->LAST_ERROR, $ID);
                $DB->Rollback();
            }
        }
        else
        {
            $lAdmin->AddGroupError(GetMessage("rub_save_error")." ".GetMessage("rub_no_rubric"), $ID);
            $DB->Rollback();
        }
        $DB->Commit();
    }
}

// обработка одиночных и групповых действий
if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT=="W")
{
    // если выбрано "Для всех элементов"
    if($_REQUEST['action_target']=='selected')
    {
        $cData = new CRubric;
        $rsData = $cData->GetList(array($by=>$order), $arFilter);
        while($arRes = $rsData->Fetch())
            $arID[] = $arRes['ID'];
    }

    // пройдем по списку элементов
    foreach($arID as $ID)
    {
        if(strlen($ID)<=0)
            continue;
        $ID = IntVal($ID);

        // для каждого элемента совершим требуемое действие
        switch($_REQUEST['action'])
        {
            // удаление
            case "delete":
                @set_time_limit(0);
                $DB->StartTransaction();
                if(!CRubric::Delete($ID))
                {
                    $DB->Rollback();
                    $lAdmin->AddGroupError(GetMessage("rub_del_err"), $ID);
                }
                $DB->Commit();
                break;

            // активация/деактивация
            case "activate":
            case "deactivate":
                $cData = new CRubric;
                if(($rsData = $cData->GetByID($ID)) && ($arFields = $rsData->Fetch()))
                {
                    $arFields["ACTIVE"]=($_REQUEST['action']=="activate"?"Y":"N");
                    if(!$cData->Update($ID, $arFields))
                        $lAdmin->AddGroupError(GetMessage("rub_save_error").$cData->LAST_ERROR, $ID);
                }
                else
                    $lAdmin->AddGroupError(GetMessage("rub_save_error")." ".GetMessage("rub_no_rubric"), $ID);
                break;
        }

    }
}

// выберем список рассылок
$cData = new ModuleFrameTable;
$rsData = $cData->GetList(['filter' => $arFilter, 'order'=>[$by=>$order]]);

// преобразуем список в экземпляр класса CAdminResult
$rsData = new CAdminUiResult($rsData, $sTableID);

// аналогично CDBResult инициализируем постраничную навигацию.
$rsData->NavStart();

// отправим вывод переключателя страниц в основной объект $lAdmin
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("rub_nav")));



$lAdmin->AddHeaders(array(
        array(  "id"    =>"ID",
            "content"  =>"ID",
            "sort"     =>"id",
            "default"  =>true,
        ),
        array(  "id"    =>"NAME",
            "content"  =>"Название",
            "sort"     =>"name",
            "default"  =>true,
        ),
        array(  "id"    =>"CODE",
            "content"  =>"Символьный код",
            "sort"     =>"code",
            "default"  =>true,
        ),
        array(  "id"    =>"DESCRIPTION",
            "content"  =>"Описание",
            "sort"     =>"description",
            "default"  =>true,
        ),
        array(  "id"    =>"DATE_INSERT",
            "content"  =>"Дата добавления",
            "sort"     =>"date_insert",
            "default"  =>true,
        ),
    )
);

while($arRes = $rsData->NavNext(true, "f_")):

    $row =& $lAdmin->AddRow($arData['ID'], $arData, 'splav_api_1c_queue_table_edit.php?ID=' . $arData['ID'] . '&lang=' . LANGUAGE_ID, "Редактировать запись");

    $arActions = array();
    if ($modulePermissions >= "W") {
        $arActions[] = array(
            "ICON" => "edit",
            "TEXT" => Loc::getMessage("MAIN_ADMIN_MENU_EDIT"),
            "DEFAULT" => true,
            "ACTION" => $lAdmin->ActionRedirect("splav_api_1c_queue_table_edit.php?ID=" . $arData['ID'] . "&lang=" . LANGUAGE_ID),
        );
        $arActions[] = array(
            "ICON" => "delete",
            "TEXT" => "Удалить",
            "ACTION" => "if(confirm('" . "Вы действительно хотите удалить запись ?" . "')) " . $lAdmin->ActionDoGroup($arData['ID'], "delete"),
        );
    }

    if (count($arActions) > 0) {
        $row->AddActions($arActions);
    }

endwhile;

// резюме таблицы
$lAdmin->AddFooter(
    array(
        array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()), // кол-во элементов
        array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"), // счетчик выбранных элементов
    )
);

// групповые действия
$lAdmin->AddGroupActionTable(Array(
    "delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"), // удалить выбранные элементы
    "activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"), // активировать выбранные элементы
    "deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"), // деактивировать выбранные элементы
));

// сформируем меню из одного пункта - добавление рассылки
$aContext = array(
    array(
        "TEXT"=>GetMessage("POST_ADD"),
        "LINK"=>"rubric_edit.php?lang=".LANG,
        "TITLE"=>GetMessage("POST_ADD_TITLE"),
        "ICON"=>"btn_new",
    ),
);

// и прикрепим его к списку
$lAdmin->AddAdminContextMenu($aContext);

// альтернативный вывод
$lAdmin->CheckListMode();

// установим заголовок страницы
$APPLICATION->SetTitle(GetMessage("rub_title"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); // второй общий пролог

$lAdmin->DisplayList();