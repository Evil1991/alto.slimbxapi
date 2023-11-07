<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;

class alto_slimbxapi extends CModule
{
    /**
     * @var string
     */
    public $MODULE_ID = 'alto.slimbxapi';

    /**
     * @var string
     */
    public $MODULE_VERSION;

    /**
     * @var string
     */
    public $MODULE_VERSION_DATE;

    /**
     * @var string
     */
    public $MODULE_NAME;

    /**
     * @var string
     */
    public $MODULE_DESCRIPTION;

    /**
     * @var string
     */
    public $PARTNER_NAME;

    /**
     * @var string
     */
    public $PARTNER_URI;

    /**
     * @var Bitrix\Main\DB\ConnectionPool
     */
    private $connection;

    /**
     * @var string
     */
    private $nameFileSupport = "";

    /**
     * Construct object
     */
    public function __construct()
    {
        $this->MODULE_NAME = 'Slim REST API';
        $this->MODULE_DESCRIPTION = 'Slim based REST API module for Bitrix';
        $this->PARTNER_NAME = '';
        $this->PARTNER_URI = '';
        $this->MODULE_PATH = $this->getModulePath();

        $arModuleVersion = array();
        include $this->MODULE_PATH . '/install/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

        $this->connection = Application::getConnection();
    }

    /**
     * Return path module
     *
     * @return string
     */
    protected function getModulePath()
    {
        $modulePath = explode('/', __FILE__);
        $modulePath = array_slice($modulePath, 0, array_search($this->MODULE_ID, $modulePath) + 1);

        return join('/', $modulePath);
    }

    /**
     * Return components path for install
     *
     * @param bool $absolute
     * @return string
     */
    protected function getComponentsPath($absolute = true)
    {
        $documentRoot = getenv('DOCUMENT_ROOT');
        if (strpos($this->MODULE_PATH, 'local/modules') !== false) {
            $componentsPath = '/local/components';
        } else {
            $componentsPath = '/bitrix/components';
        }

        if ($absolute) {
            $componentsPath = sprintf('%s%s', $documentRoot, $componentsPath);
        }

        return $componentsPath;
    }

    /**
     * Install module
     *
     * @return void
     */
    public function doInstall()
    {
        RegisterModule($this->MODULE_ID);
        // регистрируем обработчики событий
        RegisterModuleDependences("main", "OnBuildGlobalMenu", $this->MODULE_ID, "GlobalMenu", "ShowModulInMainMenu");
    }

    /**
     * Remove module
     *
     * @return void
     */
    public function doUninstall()
    {
        UnRegisterModuleDependences("main", "OnBuildGlobalMenu", $this->MODULE_ID, "GlobalMenu", "ShowModulInMainMenu");

        UnRegisterModule($this->MODULE_ID);
    }
}
