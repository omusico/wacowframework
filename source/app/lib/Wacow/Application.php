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
 * @version    $Id: Application.php 709 2009-09-21 04:05:28Z jaceju $
 */

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader/Autoloader.php';

/**
 * Autoload
 */
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

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
     * Mode of runtime
     *
     * @var string
     */
    protected $_runtimeMode = 'http';

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
     * Show Exception
     *
     * @var bool
     */
    protected $_showError = true;

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
    protected $_publicWebPath = ':pubWebPath';

    /**
     * Config of application
     *
     * @var Zend_Config
     */
    protected $_config = null;

    /**
     * Config of route
     *
     * @var Zend_Config
     */
    protected $_routeConfig = null;

    /**
     * Session save handler
     *
     * @var Zend_Session_SaveHandler_Interface
     */
    protected static $_sessionSaveHandler = null;

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
     * Constructor
     *
     */
    private function __construct()
    {}

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
            Wacow_Application::registerPlugin(new Wacow_Application_Plugin_SessionHandler());
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Register session save handler
     *
     * @param Zend_Session_SaveHandler_Interface $sessionSaveHandler
     */
    public static function registerSessionSaveHandler(Zend_Session_SaveHandler_Interface $sessionSaveHandler)
    {
        self::$_sessionSaveHandler = $sessionSaveHandler;
    }

    /**
     * Get session save handler
     *
     * @return Zend_Session_SaveHandler_Interface
     */
    public function getSessionSaveHandler()
    {
        return self::$_sessionSaveHandler;
    }

    /**
     * Register plugin
     *
     * @param Wacow_Application_Plugin_Abstract $plugin
     */
    public static function registerPlugin(Wacow_Application_Plugin_Abstract $plugin, $pluginName = null)
    {
        if (!$pluginName) {
            $pluginName = $plugin->getName();
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
    public static function addIncludePath($path)
    {
        $includePath = self::translatePath((array) $path);
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
    public static function translatePath($options)
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
    public static function run($taskName = null)
    {
        $app = self::getInstance();
        $app->_run($taskName);
    }

    /**
     * Run flow of appliction
     *
     */
    protected function _run($taskName)
    {
        if (null != $taskName) {
            $this->_runtimeMode = 'cron';
        }

        $this->_beforeRun();

        if (null === $taskName) {
            // front controller run!
            $this->_runFrontController();
        } else {
            if (is_callable($taskName)) {
                call_user_func_array($taskName, array($this));
            }
        }

        $this->_afterRun();
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
                        ->throwExceptions((bool) $this->_showError)
                        ->returnResponse(true);

        // run!
        $response = $frontController->dispatch();
        if ($response->isException()) {
            $response->renderExceptions((bool) $this->_showError);
        }
        $response->sendResponse();
    }

    /**
     * Call plugins before application running
     *
     */
    protected function _beforeRun()
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
    }

    /**
     * Call plugins after application running
     *
     */
    protected function _afterRun()
    {
        // execute plugins before run
        foreach (self::$_plugins as $plugin) {
        	/* @var $plugin Wacow_Application_Plugin_Abstract */
        	$plugin->afterRun();
        }
    }

    /**
     * Set member of application
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
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
    public function __get($name)
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
        if (null !== $section) {
            $section = (string) $section;
            if (isset($this->_config->{$section})) {
                return $this->_config->{$section};
            } else {
                return null;
            }
        } else {
            return $this->_config;
        }
    }

    /**
     * Get runtime configure by deploy mode.
     *
     * @return Zend_Config
     */
    public function getRuntimeConfig($attr = null)
    {
        $runtimeConfig =  $this->getConfig($this->_deployMode);
        if (null !== $attr) {
            if (isset($runtimeConfig->{$attr})) {
                return $runtimeConfig->{$attr};
            } else {
                return null;
            }
        } else {
            return $runtimeConfig;
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
     * Get named database options
     *
     * @param string $name
     * @return Zend_Config
     */
    public function getDbOptions($name = 'default')
    {
        return $this->getPlugin('ResourceHandler')->getDbOptions($name);
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

    /**
     * Get View
     *
     * @param string $type
     * @return Wacow_View
     */
    public function getView($viewType = 'html')
    {
        return $this->getPlugin('ResourceHandler')->getView($viewType);
    }

    /**
     * Config of Acl
     *
     * @var Zend_config
     */
    private static $_aclConfig = null;

    /**
     * Get config of acl
     *
     * @return Zend_config
     */
    protected function _buildAclConfig()
    {
        if (!self::$_aclConfig) {
             self::$_aclConfig = $this->_config->common->acl;
        }
    }

    /**
     * Is acl enabled
     *
     * @return bool
     */
    public function isAclEnabled()
    {
        $this->_buildAclConfig();
        return isset(self::$_aclConfig->enable) ? (bool) self::$_aclConfig->enable : false;
    }

    /**
     * Get acl object
     *
     * @return App_Acl_Static
     */
    public function getAcl()
    {
        $this->_buildAclConfig();
        return (isset(self::$_aclConfig->class) && class_exists(self::$_aclConfig->class))
             ? call_user_func(array(self::$_aclConfig->class, 'getInstance'))
             : false;
    }

    /**
     * Get acl options
     *
     * @return array
     */
    public function getAclOptions()
    {
        $this->_buildAclConfig();
        return isset(self::$_aclConfig->options) ? self::$_aclConfig->options->toArray() : array();
    }
}