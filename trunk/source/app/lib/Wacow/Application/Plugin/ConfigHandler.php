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
 * @subpackage Wacow_Application_Plugin
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: ConfigHandler.php 635 2009-02-25 02:43:34Z jaceju $
 */

/**
 * @see Wacow_Application_Plugin_Abstract
 */
require_once 'Wacow/Application/Plugin/Abstract.php';

/**
 * Wacow_Application_Plugin_ConfigHandler
 *
 * @category   Wacow
 * @package    Wacow_Application
 * @subpackage Wacow_Application_Plugin
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Application_Plugin_ConfigHandler extends Wacow_Application_Plugin_Abstract
{
    /**
     * Type of config file
     *
     * @var string
     */
    protected $_fileType = 'ini';

    /**
     * Path of config file
     *
     * @var string
     */
    protected $_filePath = 'config.ini';

    /**
     * Type of cache storage
     *
     * @var string
     */
    protected $_cacheStorage = 'File';

    /**
     * Path of cache store
     *
     * @var string
     */
    protected $_cacheOptions = array();

    /**
     * Config object
     *
     * @var Zend_Config
     */
    protected $_config = null;

    /**
     * Runtime Config object
     *
     * @var Zend_Config
     */
    protected $_runtimeConfig = null;

    /**
     * Runtime Config object
     *
     * @var Zend_Config
     */
    protected $_routeConfig = null;

    /**
     * Runtime Config path
     *
     * @var string
     */
    protected $_runtimePath = 'runtime.ini';

    /**
     * Router Config path
     *
     * @var string
     */
    protected $_routePath = 'route.ini';

    /**
     * Cache object
     *
     * @var Zend_Cache_Core
     */
    protected $_cache = null;

    /**
     * Constructor
     *
     * @param array $settings
     */
    public function __construct($settings)
    {
        $this->_fileType     = $settings['type'];
        $this->_filePath     = $settings['path'];
        $this->_cacheStorage = $settings['cacheStorage'];
        $this->_cacheOptions = (array) $settings['cacheOptions'];
    }

    /**
     * Handle the config with cache
     *
     */
    public function beforeRun()
    {
        // Initialize
        $this->_buildConfig();
        $this->_setCache();

        // Application config
        $this->_loadConfig('_config', 'config', 'application', $this->_filePath, '_rebuildApplicationConfig');

        // Runtime config
        if (isset($this->_config->application->runtimeConfig)) {
            $this->_runtimePath = dirname($this->_filePath) . '/' . $this->_config->application->runtimeConfig;
        } else {
            $this->_runtimePath = dirname($this->_filePath) . '/' . $this->_runtimePath;
        }

        // Route config
        if (isset($this->_config->application->routeConfig)) {
            $this->_routePath = dirname($this->_filePath) . '/' . $this->_config->application->routeConfig;
        } else {
            $this->_routePath = dirname($this->_filePath) . '/' . $this->_routePath;
        }

        $this->_buildRuntimeConfig();

        foreach ($this->_config->application as $key => $value) {
            if (!in_array($key, array('modifyDateTime', 'runtimeConfig'))) {
                $this->_app->$key = $this->_config->application->$key;
            }
        }

        // Runtime config
        if (is_file($this->_runtimePath)) {
            $this->_loadConfig('_runtimeConfig', 'runtime', 'common', $this->_runtimePath, '_rebuildRuntimeConfig');
            $this->_app->config = $this->_runtimeConfig;
        }

        // Route config
        if (is_file($this->_routePath)) {
            $this->_loadConfig('_routeConfig', 'routes', 'routes', $this->_routePath, '_rebuildRouteConfig');
            $this->_app->routeConfig = $this->_routeConfig;
        }
    }

    /**
     * Set Cache object
     *
     */
    protected function _setCache()
    {
        $setting = array(
            'frontendName'    => 'Core',
            'backendName'     => $this->_cacheStorage,
            'frontendOptions' => array('lifetime' => null, 'automatic_serialization' => true,),
            'backendOptions'  => $this->_cacheOptions,
        );
        $this->_cache = Wacow_Application_Resource::getCache($setting);
    }

    /**
     * Build config if it is not exists.
     *
     */
    protected function _buildConfig()
    {
        if (!file_exists($this->_filePath)) {
            $configContent = <<<CONTENT
[application]
; Setting of application
name          = %ProjectName%
debugMode     = true
showError     = true
defaultModule = default
deployMode    = development
CONTENT;
            file_put_contents($this->_filePath, $configContent);
        }
    }

    /**
     * Build runtime config if it is not exists.
     *
     */
    protected function _buildRuntimeConfig()
    {
        if (!file_exists($this->_runtimePath)) {
            $configContent = <<<CONTENT
[common]
; Setting of cron view
view.cron.viewDir       = ":commonPath/views"
view.cron.compileDir    = ":tmpPath/compile/cron"
view.cron.cacheDir      = ":tmpPath/html/cron"
view.cron.filters       = ""

; Setting of asset packer
asset.js.packedURL      = ":pubWebPath/js"
asset.css.packedURL     = ":pubWebPath/css"
asset.compress          = true

; Setting of acl
acl.class = App_Acl_Db
acl.enable = true
acl.options.loginHandler.module = ":moduleName"
acl.options.loginHandler.controller = user
acl.options.loginHandler.action = login
acl.options.denyHandler.module = default
acl.options.denyHandler.controller = error
acl.options.denyHandler.action = privilege

; Path of modules
module.common  = ":modulePath/common/controllers"
module.default = ":modulePath/default/controllers"
module.admin   = ":modulePath/admin/controllers"

[production]
; Databse setting
database.default.adapter         = pdo_mysql
database.default.params.host     = localhost
database.default.params.username = username
database.default.params.password = password
database.default.params.dbname   = database
database.default.params.charset  = "UTF8"
database.default.params.profiler = false

; SMTP setting
smtp.default.server   =
smtp.default.auth     =
smtp.default.username =
smtp.default.password =
smtp.default.from     =
smtp.default.charset  = "UTF-8"

[development : production]
CONTENT;
            file_put_contents($this->_runtimePath, $configContent);
        }
    }

    /**
     * Load config
     *
     * @param string $attr
     * @param string $name
     * @param string $key
     * @param string $filePath
     * @param string $method
     */
    protected function _loadConfig($attr, $name, $key, $filePath, $method)
    {
        if ($this->$attr = $this->_cache->load($name)) {
            if (!$this->_isModifiedTimeEquals($this->$attr->$key, $filePath)) {
                $this->$method();
            }
        } else {
            $this->$method();
        }
    }

    /**
     * Compare the modified time
     *
     * @param Zend_Config $config
     * @param string $filePath
     * @return bool
     */
    protected function _isModifiedTimeEquals($config, $filePath)
    {
        return $config->modifyDateTime == filemtime($filePath);
    }

    /**
     * Rebuild the config with cache
     *
     */
    /**
     * Rebuild the config with cache
     *
     * @param string $filePath
     * @param string $attr
     * @param string $name
     * @param string $key
     */
    protected function _rebuildConfig($filePath, $attr, $name, $key)
    {
        $options = array(
            'allowModifications' => true,
        );
        $this->$attr = new Zend_Config_Ini($filePath, null, $options);
        $this->$attr->$key->modifyDateTime = filemtime($filePath);

        if (false === (bool) $this->_config->application->debugMode) {
            $this->_cache->save($this->$attr, $name);
        }
    }

    /**
     * Rebuild the config with cache
     *
     */
    protected function _rebuildApplicationConfig()
    {
        $this->_rebuildConfig($this->_filePath, '_config', 'config', 'application');
    }

    /**
     * Rebuild the config with cache
     *
     */
    protected function _rebuildRuntimeConfig()
    {
        $this->_rebuildConfig($this->_runtimePath, '_runtimeConfig', 'runtime', 'common');
    }

    /**
     * Rebuild the config with cache
     *
     */
    protected function _rebuildRouteConfig()
    {
        $this->_rebuildConfig($this->_routePath, '_routeConfig', 'routes', 'routes');
    }
}