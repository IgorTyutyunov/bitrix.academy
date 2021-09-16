<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;


$module_id = 'academy.d7';

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . "/module/main/options.php");
Loc::loadMessages(__FILE__);

global $APPLICATION;

if($APPLICATION->GetGroupRight($module_id) < "S")
{
    $APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

\Bitrix\Main\Loader::includeModule($module_id);
$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

$arTabs = [
    [
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('ACADEMY_D7_TAB_SETTINGS'),
        'OPTIONS' =>[
            [
                'field_text',
                Loc::getMessage('ACADEMY_D7_FIELD_TEXT_TITLE'),
                '',
                [
                    'textarea',
                    10,
                    20
                ]
            ],
            [
                'field_line',
                Loc::getMessage('ACADEMY_D7_FIELD_LINE_TITLE'),
                '',
                [
                    'text',
                    10
                ]
            ],
            [
                'field_list',
                Loc::getMessage('ACADEMY_D7_FIELD_LIST_TITLE'),
                '',
                [
                    'multiselectbox',
                    [
                        'var1'=>'var1_description',
                        'var2'=>'var1_description',
                        'var3'=>'var1_description',
                    ]
                ]
            ],
            [
                'checkbox',
                Loc::getMessage('ACADEMY_D7_FIELD_CHECKBOX_TITLE'),
                '',
                ['checkbox']
            ]

        ]
    ],
    [
        'DIV' => 'edit2',
        'TAB' => Loc::getMessage('MAIN_TAB_RIGHTS'),
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_RIGHTS')
    ]

];

$tabControl = new CAdminTabControl('tabControl', $arTabs);
if($request->isPost() && !empty($request->get('save')) && check_bitrix_sessid())
{
    foreach ($arTabs as $arTab)
    {
        if (isset($arTab["OPTIONS"]))
            __AdmSettingsSaveOptions($module_id, $arTab['OPTIONS']);
    }
}

$tabControl->Begin();
?>
    <form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request->get('mid'))?>&lang=<?=$request->get('lang')?>"
    name="academy_d7_settings"
    >
        <?
        foreach ($arTabs as $arTab) {
            if(isset($arTab['OPTIONS']))
            {
                $tabControl->BeginNextTab();
                __AdmSettingsDrawList($module_id, $arTab['OPTIONS']);
            }
        }

        /**
         * Вкладка с правами. Чтобы она корректно работала, нежно:
         * 1. Чтобы была определена переменная $module_id - в этой переменной должен быть ID модуля
         * 2. Чтобы бы была определена переменная $Update, с любым содержимым, либо чтобы был input name='Update'.
         */
        $tabControl->BeginNextTab();
        require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/admin/group_rights.php');

        $tabControl->Buttons();
        ?>

    <input type="submit" name="Update" value="<?=Loc::getMessage('MAIN_SAVE')?>">
    <?=bitrix_sessid_post()?>
    </form>
<?$tabControl->End();