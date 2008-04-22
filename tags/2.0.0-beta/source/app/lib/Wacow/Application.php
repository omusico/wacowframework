<?php
/**
 * Wacow Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Wacow
 * @package    Wacow_Application
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Application.php 406 2008-04-20 02:43:18Z jaceju $
 */

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * Autoload
 */
Zend_Loader::registerAutoload();

/**
 * @see Wacow_Application_Plugin_PhpSetting
 */
require_once 'Wacow/Application/Plugin/PhpSetting.php';

/**
 * @see Wacow_Application_Plugin_PathHandler
 */
require_once 'Wacow/Application/Plugin/PathHandler.php';

/**
 * @see Wacow_Application_Plugin_ConstantHandler
 */
require_once 'Wacow/Application/Plugin/ConstantHandler.php';

/**
 * @see Wacow_Application_Plugin_RouterSetting
 */
require_once 'Wacow/Application/Plugin/RouterSetting.php';

/**
 * @see Wacow_Application_Plugin_ResourceHandler
 */
require_once 'Wacow/Application/Plugin/ResourceHandler.php';

/**
 * @see Wacow_Application_Plugin_ControllerPluginHandler
 */
require_once 'Wacow/Application/Plugin/ControllerPluginHandler.php';

/**
 * @see Wacow_Application_Plugin_ActionHelperManager
 */
require_once 'Wacow/Application/Plugin/ActionHelperManager.php';

/**
 * Wacow_Application
 *
 * @category   Wacow
 * @package    Wacow_Application
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Application
{
    /**
     * Reflection class
     *
     * @var ReflectionClass
     */
    protected static $_reflectionClass = null;

    /**
     * Plugins
     *
     * @var array
     */
    protected static $_plugins = array();

    /**
     * Runtime paths translator
     *
     * @var Wacow_Application_PathTranslator
     */
    protected static $_pathTranslator = null;

    /**
     * Name of application
     *
     * @var string
     */
    protected $_name = 'Wacow';

    /**
     * Debug mode
     *
     * Set true if you are developing.
     *
     * @var bool
     */
    protected $_debugMode = true;

    /**
     * Default module
     *
     * Keep the value if you don't know what is it.
     *
     * @var string
     */
    protected $_defaultModule = 'default';

    /**
     * Deploy mode
     *
     * Change the config of runtime.
     *
     * @var string
     */
    protected $_deployMode = 'development';

    /**
     * Time zone
     *
     * @var string
     */
    protected $_timezone = 'Asia/Taipei';

    /**
     * Application Mailer
     *
     * @var string
     */
    protected $_mailer = 'PHPMailer';

    /**
     * Path of public resources
     *
     * You can change the value with config.ini.
     *
     * @var string
     */
    protected $_publicPath = ':pubPath';

    /**
     * Config of application
     *
     * @var Zend_Config
     */
    protected $_config = null;

    /**
     * Singleton instance
     *
     * @param array $setting
     * @param array $paths
     * @param string $runType
     * @return Wacow_Application
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            Wacow_Application::registerPlugin(new Wacow_Application_Plugin_PhpSetting());
            Wacow_Application::registerPlugin(new Wacow_Application_Plugin_PathHandler());
            Wacow_Application::registerPlugin(new Wacow_Application_Plugin_ConstantHandler());
            Wacow_Application::registerPlugin(new Wacow_Application_Plugin_RouterSetting());
            Wacow_Application::registerPlugin(new Wacow_Application_Plugin_ResourceHandler());
            Wacow_Application::registerPlugin(new Wacow_Application_Plugin_ControllerPluginHandler());
            Wacow_Application::registerPlugin(new Wacow_Application_Plugin_ActionHelperManager());
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Wacow_Application
     */
    private static $_instance = null;

    /**
     * Register plugin
     *
     * @param Wacow_Application_Plugin_Abstract $plugin
     */
    public static function registerPlugin(Wacow_Application_Plugin_Abstract $plugin, $pluginName = null)
    {
        if (!$pluginName) {
            $className = get_class($plugin);
            $pluginName = strtolower(substr($className, strrpos($className, '_') + 1));
        }
        self::$_plugins[$pluginName] = $plugin;
    }

    /**
     * Add action helper
     *
     * @param Zend_Controller_Action_Helper_Abstract $helper
     */
    public static function addActionHelper(Zend_Controller_Action_Helper_Abstract $helper)
    {
        Zend_Controller_Action_HelperBroker::addHelper($helper);
    }

    /**
     * Settings of config
     *
     * @param array $settings
     */
    public static function setConfigSettings(array $settings)
    {
        self::registerPlugin(new Wacow_Application_Plugin_ConfigHandler($settings));
    }

    /**
     * Set runtime paths
     *
     * @param array $paths
     */
    public static function setRuntimePaths(array $paths)
    {
        self::$_pathTranslator = new Wacow_Application_PathTranslator($paths);
    }

    /**
     * Add include path
     *
     * @param string $path
     */
    public function addIncludePath($path)
    {
        $includePath = $this->translatePath((array) $path);
        if ($includePath instanceof Zend_Config) {
            $includePath = $includePath->toArray();
        }
        $includePath[] = get_include_path();
        set_include_path(join(PATH_SEPARATOR, $includePath));
    }

    /**
     * Translate paths of options
     *
     * @param array|Zend_Config $options
     * @return Zend_Config
     */
    public function translatePath($options)
    {
        return self::$_pathTranslator->translatePath($options);
    }

    /**
     * Get Front controller
     *
     * @return Zend_Controller_Front
     */
    public static function getFrontController()
    {
        return Zend_Controller_Front::getInstance();
    }

    /**
     * Application run command
     *
     * Return Wacow_Application instance
     *
     * @return Wacow_Application
     */
    public static function run()
    {
        $app = self::getInstance();
        $app->_run();
    }

    /**
     * Constructor
     *
     */
    private function __construct()
    {}

    /**
     * Run flow of appliction
     *
     */
    protected function _run()
    {
        self::$_reflectionClass = new ReflectionClass($this);

        // setting plugins
        foreach (self::$_plugins as $plugin) {
            $plugin->setApplication($this);
        }

        // execute plugins before run
        foreach (self::$_plugins as $plugin) {
        	/* @var $plugin Wacow_Application_Plugin_Abstract */
        	$plugin->beforeRun();
        }

        // front controller run!
        $this->_runFrontController();

        // execute plugins before run
        foreach (self::$_plugins as $plugin) {
        	/* @var $plugin Wacow_Application_Plugin_Abstract */
        	$plugin->afterRun();
        }
    }

    /**
     * Set front controller and dispatch
     *
     */
    protected function _runFrontController()
    {
        // setup front controller
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->setControllerDirectory($this->_config->common->module->toArray())
                        ->setDefaultModule((string) $this->_defaultModule)
                        ->throwExceptions((bool) $this->_debugMode)
                        ->returnResponse(true);

        // run!
        $response = $frontController->dispatch();
        if ($response->isException()) {
            $response->renderExceptions((bool) $this->_debugMode);
        }
        $response->sendResponse();
    }

    /**
     * Set member of application
     *
     * @param string $name
     * @param mixed $value
     */
    protected function __set($name, $value)
    {
        $memberName = '_' . $name;
        if (self::$_reflectionClass->hasProperty($memberName)) {
            $this->$memberName = $value;
        } else {
            throw new Wacow_Application_Exception("Property '$memberName' does not exist in Wacow_Application.");
        }
    }

    /**
     * Get member of application
     *
     * @param string $name
     * @return mixed
     */
    protected function __get($name)
    {
        $memberName = '_' . $name;
        if (self::$_reflectionClass->hasProperty($memberName)) {
            return $this->$memberName;
        }
    }

    /**
     * get Path translator
     *
     * @return Wacow_Application_PathTranslator
     */
    public function getPathTranslator()
    {
        return self::$_pathTranslator;
    }

    /**
     * Get plugin by name
     *
     * @param string $pluginName
     * @return Wacow_Application_Plugin_Abstract
     */
    public function getPlugin($pluginName)
    {
        $pluginName = strtolower($pluginName);
        if (array_key_exists($pluginName, self::$_plugins)) {
            return self::$_plugins[$pluginName];
        }
        return null;
    }

    /**
     * Get config object
     *
     * @param string $section
     * @return Zend_Config
     */
    public function getConfig($section = null)
    {
        if (is_string($section) && isset($this->_config->{$section})) {
            return $this->_config->{$section};
        } else {
            return $this->_config;
        }
    }

    /**
     * Get all defined constants
     *
     * @return array
     */
    public function getConstants()
    {
        return $this->getPlugin('ConstantHandler')->getConstants();
    }

    /**
     * Get named database adapter
     *
     * @param string $name
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDbAdapter($name = 'default')
    {
        return $this->getPlugin('ResourceHandler')->getDbAdapter($name);
    }

    /**
     * Get named mailer
     *
     * @param string $name
     * @return Wacow_Mail
     */
    public function getMailer($name = 'default')
    {
        return $this->getPlugin('ResourceHandler')->getMailer($name);
    }
}