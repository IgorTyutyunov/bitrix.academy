<?php
AddEventHandler('main', 'OnUserTypeBuildList', ['IgrikUserType', 'GetUserTypeDescription']);


use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Iblock;
class IgrikUserType{


    // ---------------------------------------------------------------------
    // Общие параметры методов класса:
    // @param array $arUserField - метаданные (настройки) свойства
    // @param array $arHtmlControl - массив управления из формы (значения свойств, имена полей веб-форм и т.п.)
    // ---------------------------------------------------------------------

    // Функция регистрируется в качестве обработчика события OnUserTypeBuildList
    function GetUserTypeDescription() {
        $pathCss = '/local/php_interface/my_style.css?x=' . time();
        Bitrix\Main\Page\Asset::getInstance()->addString('<link rel="stylesheet" type="text/css" href="'.$pathCss.'" />');

        $pathJs = '/local/php_interface/my_js.js?x=' . time();
        Bitrix\Main\Page\Asset::getInstance()->addJs($pathJs);

        return array(
            // уникальный идентификатор
            'USER_TYPE_ID' => 'iblock_element_list',
            // имя класса, методы которого формируют поведение типа
            'CLASS_NAME' => 'IgrikUserType',
            // название для показа в списке типов пользовательских свойств
            'DESCRIPTION' => 'Мой собственный тип',
            // базовый тип на котором будут основаны операции фильтра
            'BASE_TYPE' => 'string',
        );
    }

    // Функция вызывается при добавлении нового свойства
    // для конструирования SQL запроса создания столбца значений свойства
    // @return string - SQL
    function GetDBColumnType($arUserField) {
        switch(strtolower($GLOBALS['DB']->type)) {
            case 'mysql':
                return 'int(18)';
                break;
            case 'oracle':
                return 'number(18)';
                break;
            case 'mssql':
                return "int";
                break;
        }
    }

    // Функция вызывается перед сохранением метаданных (настроек) свойства в БД
    // @return array - массив уникальных метаданных для свойства, будет сериализован и сохранен в БД
    function PrepareSettings($arUserField) {
        // инфоблок, с элементами которого будет выполняться связь
        return array(
            'HL_ID' => 4
        );
    }

    // Функция вызывается при выводе формы метаданных (настроек) свойства
    // @param bool $bVarsFromForm - флаг отправки формы
    // @return string - HTML для вывода
    function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm) {
//        $result = '';
//
//        // добавлено 2010-12-08 (YYYY-MM-DD)
//        if(!CModule::IncludeModule('iblock')) {
//            return $result;
//        }
//
//        // текущие значения настроек
//        if($bVarsFromForm) {
//            $value = $GLOBALS[$arHtmlControl['NAME']]['IBLOCK_ID'];
//        } elseif(is_array($arUserField)) {
//            $value = $arUserField['SETTINGS']['IBLOCK_ID'];
//        } else {
//            $value = '';
//        }
//        $result .= '
//      <tr style="vertical-align: top;">
//         <td>Информационный блок по умолчанию:</td>
//         <td>
//            '.GetIBlockDropDownList($value, $arHtmlControl['NAME'].'[IBLOCK_TYPE_ID]', $arHtmlControl['NAME'].'[IBLOCK_ID]').'
//         </td>
//      </tr>
//      ';
//        return $result;
    }

    // Функция валидатор значений свойства
    // вызвается в $GLOBALS['USER_FIELD_MANAGER']->CheckField() при добавлении/изменении
    // @param array $value значение для проверки на валидность
    // @return array массив массивов ("id","text") ошибок
    function CheckFields($arUserField, $value) {
        $aMsg = array();
        return $aMsg;
    }
    function GetEditFormHTML($arUserField, $arHtmlControl) {
        $value = unserialize(htmlspecialchars_decode($arHtmlControl['VALUE']));//['VALUE' = 'UF_XML_ID', 'META' => ['title' => 'title', 'descriptipnm' => 'desc']]
        $iIBlockId = intval($arUserField['SETTINGS']['HL_ID']);
        $arRows = self::getHlRows($iIBlockId);
        $curName = $arHtmlControl['NAME'];
        ob_start();
        ?>
        <div class="mp_row">
            <select name="<?=$curName?>[VALUE]" id="">
                <option value="">Укажите регион</option>
                <?foreach ($arRows as $row):?>
                    <option value="<?=$row['UF_XML_ID']?>"
                        <?if($row['UF_XML_ID'] === $value['VALUE']):?>
                            selected="selected"
                        <?endif;?>
                    ><?=$row['UF_CODE']?></option>
                <?endforeach;?>
            </select><del></del><abbr></abbr>
            <ul>
                <li>
                    Заголовок браузера (title)
                    <div>
                        <input type="text" value="<?=$value['META']['TITLE']?>" name="<?=$curName?>[META][TITLE]" autocomplete="off">
                    </div>
                </li>
                <li>
                    Описание страницы
                    <div>
                        <input type="text" value="<?=$value['META']['DESCRIPTION']?>" name="<?=$curName?>[META][DESCRIPTION]" autocomplete="off">
                    </div>
                </li>
            </ul>
        </div>
        <?return ob_get_clean();
    }

    static $rows;
    function getHlRows($HL_ID)
    {
        if(!empty(self::$rows)) return self::$rows;
        $entity = Bitrix\Main\ORM\Entity::compileEntity(
            'HLEntity',
            [
                (new Bitrix\Main\Entity\IntegerField('ID'))->configurePrimary(),
                (new Bitrix\Main\Entity\StringField('UF_NAME')),
                (new Bitrix\Main\Entity\StringField('UF_CODE')),
                (new Bitrix\Main\Entity\StringField('UF_DESCRIPTION')),
                (new Bitrix\Main\Entity\StringField('UF_XML_ID')),
            ],
            [
                'namespace' => 'IgrikHL', 'table_name' => 'igrik_hl'
            ]
        );
        $quary = (new Bitrix\Main\ORM\Query\Query($entity))->setSelect(['*'])->exec();
        self::$rows = $quary->fetchAll();
        return self::$rows;
    }

    //Для множественных свойств желательно использовать GetEditFormHTMLMulty, но тогда я не знаю как кнопку "Добавить",
    //поэтому пришлось закостылить и использовать метод GetEditFormHTML - если используется этот метод, то кнопка "Добавить" выводится корректно.
    // Функция вызывается при выводе формы редактирования значения свойства
    // она же вызывается (в цикле) и при выводе формы редактирования множественного свойства
    // @return string - HTML для вывода
    function GetEditFormHTMLMulty_2($arUserField, $arHtmlControl) {

        $pathCss = '/local/php_interface/my_style.css?x=' . time();
        Bitrix\Main\Page\Asset::getInstance()->addString('<link rel="stylesheet" type="text/css" href="'.$pathCss.'" />');

        $pathJs = '/local/php_interface/my_js.js?x=' . time();
        Bitrix\Main\Page\Asset::getInstance()->addJs($pathJs);


        $iIBlockId = intval($arUserField['SETTINGS']['HL_ID']);
        $arRows = self::getHlRows($iIBlockId);
        ob_start();
        ?>
        <div class="my_prop">
            <?
            $i = 0;
            $arHtmlControl['VALUE'][] = false;
            foreach ($arHtmlControl['VALUE'] as $value){
                $curName = str_replace('[]', "[{$i}]", $arHtmlControl['NAME']);
                $i += 1;
                $value = unserialize(htmlspecialchars_decode($value));//['VALUE' = 'UF_XML_ID', 'META' => ['title' => 'title', 'descriptipnm' => 'desc']]
                ?>
                <div class="mp_row">
                    <select name="<?=$curName?>[VALUE]" id="">
                        <option value="">Укажите регион</option>
                        <?foreach ($arRows as $row):?>
                            <option value="<?=$row['UF_XML_ID']?>"
                                <?if($row['UF_XML_ID'] === $value['VALUE']):?>
                                    selected="selected"
                                <?endif;?>
                            ><?=$row['UF_CODE']?></option>
                        <?endforeach;?>
                    </select><del></del><abbr></abbr>
                    <ul>
                        <li>
                            Заголовок браузера (title)
                            <div>
                                <input type="text" value="<?=$value['META']['TITLE']?>" name="<?=$curName?>[META][TITLE]" autocomplete="off">
                            </div>
                        </li>
                        <li>
                            Описание страницы
                            <div>
                                <input type="text" value="<?=$value['META']['DESCRIPTION']?>" name="<?=$curName?>[META][DESCRIPTION]" autocomplete="off">
                            </div>
                        </li>
                    </ul>
                </div>
            <?}?>
        </div>
        <?
        $template = ob_get_clean();
        return $template;
    }

    // Функция вызывается при выводе фильтра на странице списка
    // @return string - HTML для вывода
    function GetFilterHTML($arUserField, $arHtmlControl) {
        //$sVal = intval($arHtmlControl['VALUE']);
        //$sVal = $sVal > 0 ? $sVal : '';
        //return '<input type="text" name="'.$arHtmlControl['NAME'].'" size="20" value="'.$sVal.'" />';
        // return IgrikUserType::GetEditFormHTML($arUserField, $arHtmlControl);
    }

    // Функция вызывается при выводе значения свойства в списке элементов
    // @return string - HTML для вывода
    function GetAdminListViewHTML($arUserField, $arHtmlControl) {
//        $iElementId = intval($arHtmlControl['VALUE']);
//        if($iElementId > 0) {
//            $arElements = IgrikUserType::_getElements($arUserField['SETTINGS']['IBLOCK_ID']);
//            // выводим в формате: [ID элемента] имя элемента (если найдено)
//            return '['.$iElementId.'] '.(isset($arElements[$iElementId]) ? $arElements[$iElementId]['NAME'] : '');
//        } else {
//            return ' ';
//        }
    }

    // Функция вызывается при выводе значения множественного свойства в списке элементов
    // @return string - HTML для вывода
    function GetAdminListViewHTMLMulty($arUserField, $arHtmlControl) {
//        $sReturn = '';
//        if(!empty($arHtmlControl['VALUE']) && is_array($arHtmlControl['VALUE'])) {
//            $arElements = IgrikUserType::_getElements($arUserField['SETTINGS']['IBLOCK_ID']);
//            $arPrint = array();
//            // выводим в формате: [ID элемента] имя элемента (если найдено) с разделителем " / " для каждого значения
//            foreach($arHtmlControl['VALUE'] as $iElementId) {
//                $arPrint[] = '['.$iElementId.'] '.(isset($arElements[$iElementId]) ? $arElements[$iElementId]['NAME'] : '');
//            }
//            $sReturn .= implode(' / ', $arPrint);
//        } else {
//            $sReturn .=  ' ';
//        }
//        return $sReturn;
    }

    // Функция вызывается при выводе значения свойства в списке элементов в режиме редактирования
    // она же вызывается (в цикле) и для множественного свойства
    // @return string - HTML для вывода
    function GetAdminListEditHTML($arUserField, $arHtmlControl) {
        // return IgrikUserType::GetEditFormHTML($arUserField, $arHtmlControl);
    }

    // Функция должна вернуть представление значения поля для поиска
    // @return string - посковое содержимое
    function OnSearchIndex($arUserField) {
//        if(is_array($arUserField['VALUE'])) {
//            return implode("\r\n", $arUserField['VALUE']);
//        } else {
//            return $arUserField['VALUE'];
//        }
    }

    // Функция вызывается перед сохранением значений в БД
    // @param mixed $value - значение свойства
    // @return string - значение для вставки в БД
    function OnBeforeSave($arUserField, $value) {
        return $value;
    }

    // Функция вызывается перед сохранением значений в БД
    // @param mixed $value - значение свойства
    // @return string - значение для вставки в БД
    function OnBeforeSaveAll($arUserField, $value) {
        $return_value = [];
        foreach ($value as $v)
        {
            if(!empty($v['VALUE']) && $v['VALUE'] != false)
            {
                $return_value[] = serialize($v);
            }

        }
        return $return_value;
    }

    // Функция генерации html для поля редактирования свойства
    // @param int $iValue - значение свойства
    // @param int $iIBlockId - ID информационного блока для поиска элементов
    // @param string $sFieldName - имя для поля веб-формы
    // @return string - HTML для вывода
    // @private
    function _getItemFieldHTML($iValue, $iIBlockId, $sFieldName) {
//        $sReturn = '';
//        // получим массив всех элементов инфоблока
//        $arElements = IgrikUserType::_getElements($iIBlockId);
//        $sReturn = '<select size="1" name="'.$sFieldName.'">
//      <option value=""> </option>';
//        foreach($arElements as $arItem) {
//            $sReturn .= '<option value="'.$arItem['ID'].'"';
//            if($iValue == $arItem['ID']) {
//                $sReturn .= ' selected="selected"';
//            }
//            $sReturn .= '>'.$arItem['NAME'].'</option>';
//        }
//        $sReturn .= '</select>';
//        return $sReturn;
    }

    // Функция генерации массива элементов тнфоблока
    // @param int $iIBlockId - ID информационного блока для поиска элементов
    // @param bool $bResetCache - перезаписать "виртуальный кэш" для инфоблока
    // @return array - массив элементов инфоблока с ключами = идентификаторам элементов инфоблока
    // @private
    function _getElements($iIBlockId = false, $bResetCache = false) {
//        static $arVirtualCache = array();
//        $arReturn = array();
//        $iIBlockId = intval($iIBlockId);
//        if(!isset($arVirtualCache[$iIBlockId]) || $bResetCache) {
//
//            // добавлено 2010-12-08 (YYYY-MM-DD)
//            if(!CModule::IncludeModule('iblock')) {
//                return $arReturn;
//            }
//
//            if($iIBlockId > 0) {
//                $arFilter = array(
//                    'IBLOCK_ID' => $iIBlockId
//                );
//                $arSelect = array(
//                    'ID',
//                    'NAME',
//                    'IBLOCK_ID',
//                    'IBLOCK_TYPE_ID'
//                );
//                $rsItems = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
//                while($arItem = $rsItems->GetNext(false, false)) {
//                    // добавлено 2011-02-15 для GetList
//                    $arItem['VALUE'] = $arItem['NAME'];
//                    $arReturn[$arItem['ID']] = $arItem;
//                }
//            }
//            $arVirtualCache[$iIBlockId] = $arReturn;
//        } else {
//            $arReturn = $arVirtualCache[$iIBlockId];
//        }
//        return $arReturn;
    }

    // добавлено 2011-02-15
    function GetList($arUserField) {
//        $dbReturn = new CDBResult;
//        $arElements = self::_getElements($arUserField['SETTINGS']['IBLOCK_ID']);
//        $dbReturn->InitFromArray($arElements);
//        return $dbReturn;
    }

}