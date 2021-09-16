<? use Bitrix\Main\Type\DateTime;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
?>

<?
$module_id = pathinfo(dirname(__DIR__))["basename"];

if ($APPLICATION->GetUserRight($module_id) <= "D") {
    return 'Доступ запрещён;';
}
IncludeModuleLangFile(__FILE__);

CModule::IncludeModule($module_id);


if ($_REQUEST["tabControl_active_tab"] == "queue_edit") {
    $arFields = array(
        "ITEM_ID" => intval($_REQUEST["ITEM_ID"]),
        "TYPE" => $_REQUEST["TYPE"],
        "DATE_INSERT" => DateTime::createFromPhp(new \DateTime($_REQUEST["DATE_INSERT"])),
        "ATTEMPT_COUNT" => (intval($_REQUEST["ATTEMPT_COUNT"]) > 0) ? intval($_REQUEST["ATTEMPT_COUNT"]) : 0,
    );

    if ($arFields["ITEM_ID"] != "" && $arFields["TYPE"] != "") {
        if ($_REQUEST["ID"] > 0) {
            $result = ModuleFrameTable::update($_REQUEST["ID"], $arFields);
        } else {
            $result = ModuleFrameTable::add($arFields);
        }
        $ID = $result->getId();
        if (!$result->isSuccess()) {
            $arErrors = $result->getErrorMessages();
        } elseif (IntVal($ID) > 0 && isset($_REQUEST["apply"])) {
            LocalRedirect( "splav_api_1c_queue_table_edit.php?lang=".LANG."&ID=".$_REQUEST["ID"] );
        } elseif(IntVal($ID) > 0 && isset($_REQUEST["save"])) {
            LocalRedirect("splav_api_1c_queue_table.php?lang=".LANG );
        }
    } else {
        $arErrors[] = "Ошибка добавления, возможно вы не заполнили одно из обязательных полей";
    }
}

// tabs list
$aTabs = array(
    array(
        "DIV" => "queue_edit",
        "TAB" => "Элемент очереди",
        "ICON" => "main_user_edit",
        "TITLE" => "Элемент очереди"
    ),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

// context menu
$aContext = array(
    array(
        "TEXT" => "К списку",
        "LINK" => "splav_api_1c_queue_table.php?lang=".LANG,
        "TITLE" => "К списку",
        "ICON" => "btn_list",
    ),
);
$oMenu = new CAdminContextMenu($aContext);

// set page title
$APPLICATION->SetTitle("Элемент очереди");

// include prolog
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// show errors
if (count($arErrors) > 0) {
    CAdminMessage::ShowMessage( implode('<br />', $arErrors) );
}

// show context menu
$oMenu->Show();

// show form
?>
<form id="bpg" method="POST" action="<?=$APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="queue_1c">
<?

// tabs header
$tabControl->Begin();

//___________________________________________________________________________________________
// tab
//___________________________________________________________________________________________
if (IntVal($_REQUEST["ID"]) > 0) {
    $dbRes = ModuleFrameTable::GetByID(IntVal($_REQUEST["ID"]));
    $arQueueItem = $dbRes->Fetch();
}

$tabControl->BeginNextTab();
?>

<?if($arQueueItem["ID"] > 0):?>
    <input type="hidden" name="ID" value="<?=$arQueueItem["ID"]?>" />
    <tr>
        <td>ID:</td>
        <td><?=$arQueueItem["ID"]?></td>
    </tr>
<?endif;?>
    <tr>
        <td width="40%" valign="top" align="right"><span class="adm-required-field">ID элемента очереди:</span></td>
        <td width="60%"><input type="text" name="ITEM_ID" value="<?=$arQueueItem["ITEM_ID"]?>" /></td>
    </tr>
    <tr>
        <td width="40%" valign="top" align="right"><span class="adm-required-field">Тип элемента:</span></td>
        <td width="60%"><input size="100" type="text" name="TYPE" value="<?=$arQueueItem["TYPE"]?>" /></td>
    </tr>
    <tr>
        <td width="40%" valign="top" align="right"><span>Дата добавления в очередь:</span></td>
        <td width="60%"><?echo CalendarDate("DATE_INSERT", $arQueueItem["DATE_INSERT"], "queue_1c", "15")?></td>
    </tr>
    <tr>
        <td width="40%" valign="top" align="right"><span>Количество попыток:</span></td>
        <td width="60%"><input size="100" type="text" name="ATTEMPT_COUNT" value="<?=$arQueueItem["ATTEMPT_COUNT"]?>" /></td>
    </tr>
    <input type="hidden" name="lang" value="<?=LANG?>">
<?
// tab bottons
$tabControl->Buttons(
    array(
        "back_url" => "splav_api_1c_queue_table.php?lang=".LANG,
        "btnApply" => intval($arQueueItem["ID"]) > 0 ? true : false,
    )
);

// tab footer
$tabControl->End();

// include epilog
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>