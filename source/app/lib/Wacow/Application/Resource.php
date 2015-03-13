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
 * @version    $Id: Resource.php 610 2009-01-05 03:00:55Z jaceju $
 */

/**
 * @see Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * @see Zend_Db
 */
require_once 'Zend/Db.php';

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * Create resource for web application.
 *
 * @category   Wacow
 * @package    Wacow_Application
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
final class Wacow_Application_Resource
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
     * $cache = Wacow_Application_Resource::getCache($setting);
     * </code>
     *
     * @param array $setting
     * @return Zend_Cache_Core
     */
    public static function getCache($setting)
    {
        if ($setting instanceof Zend_Config) {
            $setting = $setting->toArray();
        }

        $frontendName    = 'Core';
        $backendName     = 'File';
        $frontendOptions = array();
        $backendOptions  = array();

        // set frontend type
        if (isset($setting['frontendName']) && in_array($setting['frontendName'], Zend_Cache::$availableFrontends)) {
            $frontendName = $setting['frontendName'];
        }

        // set backend type
        if (isset($setting['backendName']) && in_array($setting['backendName'], Zend_Cache::$availableBackends)) {
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
     * $config = Wacow_Application_Resource::getConfig($setting);
     * </code>
     *
     * @var Zend_Config $config
     * @param array $setting
     * @return Zend_Config
     */
    public function getConfig($setting)
    {
        if ($setting instanceof Zend_Config) {
            $setting = $setting->toArray();
        }

        // params for build config object
        $path    = null;
        $type    = null;
        $data    = array();
        $section = null;
        $config  = false;
        $allowModifications  = false;
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

        // if config given
        if (isset($setting['config']) && is_array($setting['config'])) {
            $config = $setting['config'];
        }

        // is allow modification
        if (isset($setting['allowModifications']) && is_bool($setting['allowModifications'])) {
            $allowModifications = $setting['allowModifications'];
        }

        // return config object
        if (!$path && !$type) {
            return new Zend_Config($data, $allowModifications);
        } else {
            $className = 'Zend_Config_' . $type;
            return new $className($path, $section, $config);
        }
    }

    /**
     * Get database adapter object
     *
     * Here is an example to get a db adapter with pdo_mysql:
     * <code>
     * $options = array(
     *     'type'    => 'pdo_mysql',  // default type is 'pdo_mysql'
     *     'setting' => array(
     *         'host'     => 'localhost',
     *         'username' => 'xxx',
     *         'password' => 'xxx',
     *         'dbname'   => 'wacow',
     *     ),
     * );
     * $db = Wacow_Application_Resource::getDbAdapter($options);
     * Zend_Db_Table::setDefaultAdapter($db);
     * </code>
     *
     * @param string $options
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDbAdapter($options)
    {
        // params for build adapter object
        $dbAdapter = null;
        $type      = null;
        $charset   = null;

        try {
            if (is_array($options)) {
                // get adapter type
                if (isset($options['type']) && is_string($options['type'])) {
                    $type = strtolower($options['type']);
                }

                // get adapter charset
                if (isset($options['setting']['charset'])) {
                    $charset = $options['setting']['charset'];
                }

                // create adapter
                $dbAdapter = Zend_Db::factory($type, $setting);
            } elseif ($options instanceof Zend_Config) {
                $dbAdapter = Zend_Db::factory($options);
                $type = $options->adapter;
                $charset = $options->params->charset;
            }

            // correct mysql charset
            // remove code below when Zend_Db resolved problem of mysql charset
            if ((bool) preg_match('/mysql/i', $type) && isset($charset)) {
                $dbAdapter->query('SET NAMES ' . $charset);
            }
        } catch (Exception $e) {
            throw $e;
        }

        if (null === $dbAdapter) {
            throw new Zend_Db_Adapter_Exception('Database connection failed.');
        }

        return $dbAdapter;
    }

    /**
     * Get mailer object
     *
     * Here is an example to get a mailer:
     * <code>
     * $options = array(
     *     'server'   => 'localhost',  // default server is 'localhost'
     *     'auth'     => 'login',
     *     'username' => 'xxxxx',
     *     'password' => 'xxxxx',
     *     'from'     => 'xxx@xxx.xxx',
     * );
     * $mailer = Wacow_Application_Resource::getMailer($options);
     * </code>
     *
     * @param string $options
     * @return Wacow_Mail
     */
    public function getMailer($type, $options)
    {
        if (empty($type) || !is_string($type)) {
            throw new Wacow_Application_Exception('Need the type of mailer.');
        }

        // params for build adapter object
        $server  = 'localhost';

        // convert to array
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        // get smtp server
        if (isset($options['server']) && is_string($options['server'])) {
            $server = strtolower($options['server']);
        }
        unset($options['server']);

        // create mailer
        $className = 'Wacow_Mail_' . $type;
        $mailer = new $className($server, $options);

        return $mailer;
    }
}