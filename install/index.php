<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Loader;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\File;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Alto\Slimbxapi\Models\ApiJwtTokensTable;


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
     * ORM-classes
     * @var array
     */
    private $classes = [];

    /**
     * @var EventManager
     */
    private $events;

    /**
     * @var array
     */
    private $files = [];

    /**
     * @var string
     */
    private $api_path = "";

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

        $this->events = EventManager::getInstance();
        $this->connection = Application::getConnection();

        $this->classes = [
            ApiJwtTokensTable::class
        ];

        $this->files = $this->getFilesPath();
        $this->api_path = Application::getDocumentRoot() . '/api';
    }

    /**
     * Install module
     *
     * @return void
     * @throws \Bitrix\Main\LoaderException
     */
    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        Loader::includeModule($this->MODULE_ID);

        $this->installDB();
        $this->installEvents();
        $this->installFiles();
    }

    /**
     * Remove module
     *
     * @return void
     * @throws \Bitrix\Main\LoaderException
     */
    public function doUninstall()
    {
        Loader::includeModule($this->MODULE_ID);

        $this->unInstallDB();
        $this->unInstallEvents();
        $this->unInstallFiles();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * Install DB tables
     *
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function installDB()
    {
        foreach ($this->classes as $class) {
            if (!$this->connection->isTableExists(Base::getInstance($class)->getDBTableName())) {
                Base::getInstance($class)->createDBTable();
            }
        }
    }

    /**
     * Uninstall DB tables
     *
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\DB\SqlQueryException
     * @throws \Bitrix\Main\SystemException
     */
    public function unInstallDB()
    {
        foreach ($this->classes as $class) {
            $table = Base::getInstance($class)->getDBTableName();
            if ($this->connection->isTableExists($table)) {
                $this->connection->dropTable($table);
            }
        }
    }

    /**
     * Install module files
     *
     * @return void
     */
    public function installFiles()
    {
        if (!is_dir($this->api_path)) {
            Directory::createDirectory($this->api_path);
        }

        copy($this->files['base'], $this->api_path. '/slimbxapi.php');
        CopyDirFiles($this->files['swagger'], $this->api_path . '/swagger', true, true);

        // TODO: решить вопрос с расположением composer
        //CopyDirFiles($this->files['composer'], Application::getDocumentRoot() . '/local', true, true);
    }

    /**
     * Uninstall module files
     *
     * @return void
     */
    public function unInstallFiles()
    {
        if (is_dir($this->api_path)) {
            Directory::deleteDirectory($this->api_path);
        }
    }

    /**
     * Register events
     *
     * @return void
     */
    public function installEvents()
    {
        // TODO: а нужно ли?
        $this->events->registerEventHandlerCompatible(
            'main',
            'OnBuildGlobalMenu',
            $this->MODULE_ID,
            'GlobalMenu',
            'ShowModulInMainMenu'
        );
    }

    /**
     * Unregister events
     *
     * @return void
     */
    public function unInstallEvents()
    {
        $this->events->unRegisterEventHandler(
            'main',
            'OnBuildGlobalMenu',
            $this->MODULE_ID,
            'GlobalMenu',
            'ShowModulInMainMenu'
        );
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
     * @return array
     */
    protected function getFilesPath(): array
    {
        $dir = $this->getModulePath() . '/dist/';
        $swagger = $dir . 'swagger/';

        if (version_compare(phpversion(), '8.0', '<')) {
            $composer = $dir . 'composer/php7.4/';
        } else {
            $composer = $dir . 'composer/php8.0/';
        }

        return [
            'base' => $dir . 'slimbxapi.php',
            'swagger' => $swagger,
            'composer' => $composer
        ];
    }
}
