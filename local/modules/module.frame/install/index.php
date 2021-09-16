<?
IncludeModuleLangFile(__FILE__);
use \Bitrix\Main\EventManager;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Config\Option;

Class Module_Frame extends CModule
{

    var $MODULE_ID = "module.frame";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $errors;

    function __construct()
    {
        $this->MODULE_VERSION = "1.0.0";
        $this->MODULE_VERSION_DATE = "10.09.2021";
        $this->MODULE_NAME = "Тут название модуля";
        $this->MODULE_DESCRIPTION = "Тут описание модуля";
    }

    function DoInstall()
    {
        $this->InstallDB();
        $this->InstallEvents();
        $this->InstallFiles();
        $this->InstallOptions();
        $this->InstallAgents();
        \Bitrix\Main\ModuleManager::RegisterModule($this->MODULE_ID);
        return true;
    }

    function DoUninstall()
    {
        $this->UnInstallAgents();
        $this->UnInstallEvents();
        $this->UnInstallOptions();
        $this->UnInstallFiles();
        $this->UnInstallDB();
        \Bitrix\Main\ModuleManager::UnRegisterModule($this->MODULE_ID);
        return true;
    }

    function InstallDB()
    {
        global $DB, $DBType;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/local/modules/" . $this->MODULE_ID . "/install/db/".$DBType."/install.sql");
        if (!$this->errors) {
            return true;
        } else
            return $this->errors;
    }

    function UnInstallDB()
    {
        global $DB, $DBType;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/local/modules/" . $this->MODULE_ID . "/install/db/".$DBType."/uninstall.sql");
        if (!$this->errors) {
            return true;
        } else
            return $this->errors;
    }

    public function InstallEvents()
    {

    }

    public function UnInstallEvents()
    {
    }

    public function InstallOptions()
    {

        return true;
    }

    public function UnInstallOptions()
    {
        return true;
    }

    public function InstallAgents()
    {
    }

    public function UnInstallAgents()
    {

    }

    public function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
        return true;
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
        return true;
    }
}
