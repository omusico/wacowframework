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
 * @package    Wacow_Resource
 * @copyright
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * Zend_Db
 */
require_once 'Zend/Db.php';

/**
 * Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * Create resource for web application.
 *
 * @category   Wacow
 * @package    Wacow_Resource
 * @copyright
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
final class Wacow_Resource
{
    /**
     * Get cache
     *
     * Here is an example to get a core cache with file storage:
     * <code>
     * $setting = array(
     *     'frontendName'    => 'Core',
     *     'backendName'     => 'File',
     *     'frontendOptions' => array('caching'   => true),
     *     'backendOptions'  => array('cache_dir' => '/path/to/cache'),
     * );
     * $cache = Wacow_Resource::getCache($setting);
     * </code>
     *
     * @param array $setting
     * @return Zend_Cache
     */
    public static function getCache($setting)
    {
        $frontendName    = 'Core';
        $backendName     = 'File';
        $frontendOptions = array();
        $backendOptions  = array();

        // set frontend type
        if (isset($setting['frontendName']) && in_array($setting['frontendName'], Zend_Cache::$availableFrontends)) {
            $frontendName = $setting['frontendName'];
        }

        // set backend type
        if (isset($setting['backendName']) && in_array($setting['backendName'], Zend_Cache::$$availableBackends)) {
            $backendName = $setting['backendName'];
        }

        // set frontend options
        if (isset($setting['frontendOptions']) && is_array($setting['frontendOptions'])) {
            $frontendOptions = $setting['frontendOptions'];
        }

        // set backend options
        if (isset($setting['backendOptions']) && is_array($setting['backendOptions'])) {
            $backendOptions = $setting['backendOptions'];
        }

        return Zend_Cache::factory($frontendName, $backendName, $frontendOptions, $backendOptions);
    }

    /**
     * Get config object
     *
     * Here is an example to get a config object from '/path/to/config.ini' with ini type:
     * <code>
     * $setting = array(
     *     'type'    => 'ini',
     *     'path'    => '/path/to/config.ini',
     *     'section' => 'production',
     * );
     * $config = Wacow_Resource::getConfig($setting);
     * </code>
     *
     * @var Zend_Config $config
     * @param array $setting
     * @return Zend_Config
     */
    public function getConfig($setting)
    {
        // params for build config object
        $path  = null;
        $type  = null;
        $data     = array();
        $section     = null;
        $availableConfigType = array('ini', 'xml');

        // get config file path
        if (isset($setting['path']) && is_file($setting['path'])) {
            $path = $setting['path'];
        }

        // get config file type
        if (isset($setting['type']) && in_array($setting['type'], $availableConfigType)) {
            $type = ucfirst(strtolower($setting['type']));
        }

        // get section name
        if (isset($setting['section']) && is_string($setting['section'])) {
            $section = $setting['section'];
        }

        // if raw data given
        if (isset($setting['data']) && is_array($setting['data'])) {
            $data = $setting['data'];
        }

        // return config object
        if (!$path && !$type) {
            return new Zend_Config($data);
        } else {
            $className = 'Zend_Config_' . $type;
            return new $className($path, $section);
        }
    }

    /**
     * Get database adapter object
     *
     * Here is an example to get a db adapter with pdo_mysql which named 'production':
     * <code>
     * $setting = array(
     *     'name'    => 'production', // default name is 'db'
     *     'type'    => 'pdo_mysql',  // default type is 'pdo_mysql'
     *     'options' => array(
     *         'host'     => 'localhost',
     *         'username' => 'xxx',
     *         'password' => 'xxx',
     *         'dbname'   => 'wacow',
     *     ),
     * );
     * $db = Wacow_Resource::getDbAdapter($setting);
     * Zend_Db_Table::setDefaultAdapter($db);
     * </code>
     *
     * Then get the db adapter which named 'production' in action controller:
     * <code>
     * $db = Wacow_Resource::getDbAdapter('production');
     * </code>
     *
     * @param string $settingOrName
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDbAdapter($settingOrName = 'db')
    {
        // if adapter is existed
        if (is_string($settingOrName)
                && ($db = Zend_Registry::get($settingOrName))
                && ($db instanceof Zend_Db_Adapter_Abstract)) {
            return $db;
        }

        // is setting
        $setting = $settingOrName;

        // params for build adapter object
        $name    = 'db';
        $type    = 'pdo_mysql';
        $options = array();

        // get adapter name
        if (isset($setting['name']) && is_string($setting['name'])) {
            $name = $setting['name'];
        }

        // get adapter type
        if (isset($setting['type']) && is_string($setting['type'])) {
            $type = strtolower($setting['type']);
        }

        // get adapter options
        if (isset($setting['options'])) {
            $options = $setting['options'];
        }

        // return adapter
        $dbAdapter = Zend_Db::factory($type, $options);
        Zend_Registry::set($name, $dbAdapter);
        return $dbAdapter;
    }

    public function getView($type, $options = array())
    {

    }

    public function getDataSet($type, $options = array())
    {

    }
}