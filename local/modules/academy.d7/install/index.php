<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

Loc::getIncludedFiles(__FILE__);

class academy_d7 extends CModule
{
    var $exclusionAdminFiles;

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/version.php');

        $this->MODULE_ID = 'academy.d7';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('ACADEMY_D7_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('ACADEMY_D7_MODULE_DESCRIPTION');

        $this->PARTNER_NAME = Loc::getMessage('ACADEMY_D7_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('ACADEMY_D7_PARTNER_URI');

        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = 'Y';


    }

    function DoInstall()
    {
        Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

        $this->InstallDB();
        $this->InstallFiles();
        $this->InstallEvents();
        $this->InstallOptions();
        global $APPLICATION;
        $APPLICATION->IncludeAdminFile(Loc::getMessage('ACADEMY_D7_INSTALL_TITLE'), __DIR__ . rls('/step.php'));
    }

    function DoUninstall()
    {

        $this->UnInstallOptions();
        global $APPLICATION;
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        if($request->get('step') < 2)
        {
            $APPLICATION->IncludeAdminFile(Loc::getMessage('ACADEMY_D7_UNINSTALL_TITLE_STEP_1'), __DIR__ . rls('/unstep1.php'));
        }
        elseif ($request->get('step') == 2)
        {
            $this->UnInstallFiles();
            $this->UnInstallEvents();

            if($request->get('savedata') != 'Y')
            {
                $this->UnInstallDB();
            }

            Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
            $APPLICATION->IncludeAdminFile(Loc::getMessage('ACADEMY_D7_UNINSTALL_TITLE_STEP_2'), __DIR__ . rls('/unstep2.php'));
        }
    }



    function InstallDB()
    {
        \Bitrix\Main\Loader::includeModule($this->MODULE_ID);

        /**
         * Создание таблицы в БД для ORM \Academy\D7\DB\MySql\BookTable если таблицы не существовало.
         * @var $entityBookTable Bitrix\Main\ORM\Entity
         */
        $connection = \Bitrix\Main\Application::getConnection(\Academy\D7\DB\MySql\BookTable::getConnectionName());
        $entityBookTable = Academy\D7\DB\MySql\BookTable::getEntity();
        if(!$connection->isTableExists($entityBookTable->getDBTableName()))
        {
            $entityBookTable->createDbTable();
        }
    }

    function UnInstallDB()
    {
        \Bitrix\Main\Loader::includeModule($this->MODULE_ID);

        /**
         * Удаление таблицы в БД для ORM \Academy\D7\DB\MySql\BookTable
         * @var $entityBookTable Bitrix\Main\ORM\Entity
         */
        $connection = \Bitrix\Main\Application::getConnection(\Academy\D7\DB\MySql\BookTable::getConnectionName());
        $entityBookTable = Academy\D7\DB\MySql\BookTable::getEntity();
        $connection->queryExecute('drop table if exists ' . $entityBookTable->getDBTableName());
    }


    function InstallEvents()
    {
        /**
         * Пример регистрации обработчика событий.
         * Но для ORM текущешл модуля так не стоит делать,
         * так как обрабатывать события ORM текущего модуля можно с помощью функций в классе ORM.
         */
        Bitrix\Main\EventManager::getInstance()->registerEventHandler($this->MODULE_ID, '\Academy\D7\DB\MySql\Book::OnBeforeAdd', $this->MODULE_ID, 'Academy\D7\DB\MySql\BookTable','EventHandlerBeforeAdd');
    }

    function UnInstallEvents()
    {
        /**
         * Привер удаления зарегистрированного обработчика событий
         */
        Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler($this->MODULE_ID, '\Academy\D7\DB\MySql\Book::OnBeforeAdd', $this->MODULE_ID, 'Academy\D7\DB\MySql\BookTable','EventHandlerBeforeAdd');
    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/" . $this->MODULE_ID . "/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
        return true;
    }

    public function InstallOptions()
    {
        return true;
    }

    public function UnInstallOptions()
    {
        /**
         * Если нужно удалить все настройки модуля
         */
        Option::delete($this->MODULE_ID);
        return true;
    }

    /**
     * Если нужно прописать свои права доступа к модулю
     * @return string[][]
     */
    function GetModuleRightList()
    {
        $arr = [
            "reference_id" => ["D","K","S","W"],
            "reference" => [

                "[D] "."Доступ закрыт",
                "[K] "."Доступ к компонентам модуля",
                "[S] "."Доступ к настройкам",
                "[W] "."Полный доступ"
            ]
        ];
        return $arr;
    }
}
