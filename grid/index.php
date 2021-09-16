<?php
include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Тестовый список");
Bitrix\Main\Page\Asset::getInstance()->addCss('/bitrix/css/main/grid/webform-button.css');

use \Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;

$list_id = 'igrik_hl';

$grid_options = new GridOptions($list_id);
$sort = $grid_options->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();


$nav = new PageNavigation('request_list');//id - можно любой написать. он будет фигурировать в качестве GET параметра, в который пепедаётся старница
$nav->allowAllRecords(true)
    ->setPageSize($nav_params['nPageSize'])
    ->initFromUri();

$ui_filter = [
    ['id' => 'ID', 'name' => 'ID', 'type'=>'integer', 'default' => true],
    ['id' => 'UF_NAME', 'name' => 'Название', 'type'=>'text', 'default' => true],
    ['id' => 'UF_CODE', 'name' => 'Код', 'type'=>'text', 'default' => true],
    ['id' => 'UF_DESCRIPTION', 'name' => 'Описание', 'type'=>'text', 'default' => true],
    ['id' => 'UF_FULL_DESCRIPTION', 'name' => 'Полное Описание', 'type'=>'text', 'default' => true],
    //['id' => 'DATE_CREATE', 'name' => 'Дата создания', 'type'=>'date', 'default' => true],
];
$filterOption = new Bitrix\Main\UI\Filter\Options($list_id);
$filterData = $filterOption->getFilter($ui_filter);
$filter = [];
$arHeaders = array_column($ui_filter, 'id');
foreach ($filterData as $k => $v) {
    if (in_array($k, $arHeaders))
    {
        $filter[$k] = $v;
    }
}

$res = igrikTable::getList([
    'filter' => $filter,
    'select' => [
        "*",
    ],
    'offset'      => $nav->getOffset(),
    'limit'       => $nav->getLimit(),
    'order'       => $sort['sort'],
    "count_total" => true,
]);
// засовываем общее количество записей в объект пагинации, перед выбором нужных объектов
$nav->setRecordCount($res->getCount());//без этого не будет работать пагинация. Убил хер знает сколько, поежде чем починить пагинацию
?>
    <h2>Фильтр</h2>
    <div>
        <?$APPLICATION->IncludeComponent('bitrix:main.ui.filter', '', [
            'FILTER_ID' => $list_id,
            'GRID_ID' => $list_id,
            'FILTER' => $ui_filter,
            'ENABLE_LIVE_SEARCH' => true,
            'ENABLE_LABEL' => true
        ]);?>
    </div>
    <div style="clear: both;"></div>

    <hr>

    <h2>Таблица</h2>
<?php
$columns = [];
$columns[] = ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true];
$columns[] = ['id' => 'UF_NAME', 'name' => 'Название', 'sort' => 'UF_NAME', 'default' => true];
$columns[] = ['id' => 'UF_CODE', 'name' => 'Код', 'sort' => 'UF_CODE', 'default' => true];
$columns[] = ['id' => 'UF_DESCRIPTION', 'name' => 'Описание', 'sort' => 'UF_DESCRIPTION', 'default' => true];
$columns[] = ['id' => 'UF_FULL_DESCRIPTION', 'name' => 'Полное описание', 'sort' => 'UF_FULL_DESCRIPTION', 'default' => true];
//$columns[] = ['id' => 'DATE_CREATE', 'name' => 'Создано', 'sort' => 'DATE_CREATE', 'default' => true];

foreach ($res->fetchAll() as $row) {
    $list[] = [
            'id' => $row['ID'],
        'data' => [
            "ID" => $row['ID'],
            "UF_NAME" => $row['NAME'],
            "UF_CODE" => $row['UF_CODE'],
            "UF_DESCRIPTION" => $row['UF_DESCRIPTION'],
            "UF_FULL_DESCRIPTION" => $row['UF_FULL_DESCRIPTION'],
            //"DATE_CREATE" => $row['DATE_CREATE'],
        ],
        'actions' => [
            [
                'text'    => 'Просмотр',
                'default' => true,
                'onclick' => 'document.location.href="?op=view&id='.$row['ID'].'"'
            ], [
                'text'    => 'Удалить',
                'default' => true,
                'onclick' => 'if(confirm("Точно?")){document.location.href="?op=delete&id='.$row['ID'].'"}'
            ]
        ]
    ];
}

$snippets = new \Bitrix\Main\Grid\Panel\Snippet();
$onchange = new \Bitrix\Main\Grid\Panel\Snippet\Onchange();
$onchange->addAction(
    [
        'ACTION' =>  Bitrix\Main\Grid\Panel\Actions::CALLBACK,
        'CONFIRM' => true,
        'CONFIRM_APPLY_BUTTON'  => 'Подтвердить',
        'DATA' => [
            ['JS' => 'Grid.removeSelected()']
        ]
    ]
);
$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => $list_id,
    'COLUMNS' => $columns,
    'ROWS' => $list,
    'SHOW_ROW_CHECKBOXES' => true,
    'NAV_OBJECT' => $nav,
    'AJAX_MODE' => 'Y',
    'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
    'PAGE_SIZES' =>  [
        ['NAME' => '20', 'VALUE' => '20'],
        ['NAME' => '50', 'VALUE' => '50'],
        ['NAME' => '100', 'VALUE' => '100']
    ],
    'AJAX_OPTION_JUMP'          => 'N',
    'SHOW_CHECK_ALL_CHECKBOXES' => false,
    'SHOW_ROW_ACTIONS_MENU'     => true,
    'SHOW_GRID_SETTINGS_MENU'   => true,
    'SHOW_NAVIGATION_PANEL'     => true,
    'SHOW_PAGINATION'           => true,
    'SHOW_SELECTED_COUNTER'     => true,
    'SHOW_TOTAL_COUNTER'        => true,
    'SHOW_PAGESIZE'             => true,
    'SHOW_ACTION_PANEL'         => true,
    'ALLOW_COLUMNS_SORT'        => true,
    'ALLOW_COLUMNS_RESIZE'      => true,
    'ALLOW_HORIZONTAL_SCROLL'   => true,
    'ALLOW_SORT'                => true,
    'ALLOW_PIN_HEADER'          => true,
    'AJAX_OPTION_HISTORY'       => 'N',
    'ACTION_PANEL' => [
        'GROUPS' => [
                'TYPE' => [
                'ITEMS' => [
                        $snippets->getRemoveButton(),
                        $snippets->getForAllCheckbox(),
                        $snippets->getEditButton(),
                ]]
        ]
    ],
//    'ACTION_PANEL'              => [
//        'GROUPS' => [
//            'TYPE' => [
//                'ITEMS' => [
//                    [
//                        'ID'    => 'set-type',
//                        'TYPE'  => 'DROPDOWN',
//                        'ITEMS' => [
//                            ['VALUE' => '', 'NAME' => '- Выбрать -'],
//                            ['VALUE' => 'plus', 'NAME' => 'Поступление'],
//                            ['VALUE' => 'minus', 'NAME' => 'Списание']
//                        ]
//                    ],
//                    [
//                        'ID'       => 'edit',
//                        'TYPE'     => 'BUTTON',
//                        'TEXT'        => 'Редактировать',
//                        'CLASS'        => 'icon edit',
//                        'ONCHANGE' => ''
//                    ],
//                    [
//                        'ID'       => 'delete',
//                        'TYPE'     => 'BUTTON',
//                        'TEXT'     => 'Удалить',
//                        'CLASS'    => 'icon remove',
//                        'ONCHANGE' => $onchange->toArray()
//                    ],
//                ],
//            ]
//        ],
//    ],
]);
?>
    <script type="text/javascript">
        BX.addCustomEvent('BX.Main.Filter:apply', BX.delegate(function (command, params) {
            var workarea = $('#' + command); // в command будет храниться GRID_ID из фильтра

            $.post(window.location.href, function(data){
                workarea.html($(data).find('#' + command).html());
            })
        }));
    </script>
    <script type="text/javascript">
        var reloadParams = { apply_filter: 'Y', clear_nav: 'Y' };
        var gridObject = BX.Main.gridManager.getById('igrik_hl'); // Идентификатор грида

        if (gridObject.hasOwnProperty('instance')){
            gridObject.instance.reloadTable('POST', reloadParams);
        }
    </script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>