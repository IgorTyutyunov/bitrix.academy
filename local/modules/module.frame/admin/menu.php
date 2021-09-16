<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
$aMenu = array(
    array(
        'parent_menu' => 'global_menu_settings',
        'sort' => 450,
        'text' => "Настройки модуля КАРКАС МОДУЛЯ",
        'title' => "",
        'url' => 'settings.php?lang=ru&mid=module.frame'
    ),
    array(
        'parent_menu' => 'global_menu_store',
        'sort' => 150,
        'text' => "Список элементов таблицы",
        'title' => "",
        'url' => '/bitrix/admin/table_list.php',
        'icon' => 'update_menu_icon',
        "items_id" => "splav_api_1c_queue_table",
        "more_url"=>array('splav_api_1c_queue_table_edit.php'),
    ),

);
return $aMenu;
