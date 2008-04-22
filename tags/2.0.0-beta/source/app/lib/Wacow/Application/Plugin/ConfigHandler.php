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
 * @version    $Id: ConfigHandler.php 406 2008-04-20 02:43:18Z jaceju $
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
     * Path of cache store
     *
     * @var string
     */
    protected $_cachePath = '/var';

    /**
     * Config object
     *
     * @var Zend_Config
     */
    protected $_config = null;

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
//        if (!is_array($settings)) {
//            /**
//             * @see Wacow_Application_Exception
//             */
//            require_once 'Wacow/Application/Exception.php';
//            throw new Wacow_Application_Exception('ConfigHandler parameters must be in an array');
//        }
//
//        if (!array_key_exists('type', $settings) || !is_string($settings['type'])) {
//            throw new Wacow_Application_Exception('Config type must be specified in a string');
//        }
//
//        if (!array_key_exists('path', $settings)) {
//            throw new Wacow_Application_Exception('Config path must be specified in a readable file');
//        }
//
//        if (!array_key_exists('cachePath', $settings)) {
//            throw new Wacow_Application_Exception('Config cache path must be specified in a writable path');
//        }

        $this->_fileType  = $settings['type'];
        $this->_filePath  = $settings['path'];
        $this->_cachePath = $settings['cachePath'];
    }

    /**
     * Handle the config with cache
     *
     */
    public function beforeRun()
    {
        $this->_setCache();

        if ($this->_config = $this->_cache->load('config')) {
            if (!$this->_isModifiedTimeEquals()) {
                $this->_rebuildCache();
            }
        } else {
            $this->_rebuildCache();
        }

        foreach ($this->_config->application as $key => $value) {
            if ($key != 'modifyDateTime') {
        	   $this->_app->$key = $this->_config->application->$key;
            }
        }
        unset($this->_config->application);
        $this->_app->config = $this->_config;
    }

    /**
     * Set Cache object
     *
     */
    protected function _setCache()
    {
        $setting = array(
            'frontendName'    => 'Core',
            'backendName'     => 'File',
            'frontendOptions' => array('lifetime' => null, 'automatic_serialization' => true,),
            'backendOptions'  => array('cache_dir' => $this->_cachePath,),
        );
        $this->_cache = Wacow_Application_Resource::getCache($setting);
    }

    /**
     * Compare the modified time
     *
     * @return bool
     */
    protected function _isModifiedTimeEquals()
    {
        return $this->_config->application->modifyDateTime == filemtime($this->_filePath);
    }

    /**
     * Rebuild the config with cache
     *
     */
    protected function _rebuildCache()
    {
        $options = array(
            'allowModifications' => true,
        );
        $this->_config = new Zend_Config_Ini($this->_filePath, null, $options);
        $this->_config->application->modifyDateTime = filemtime($this->_filePath);

        if (false === (bool) $this->_config->application->debugMode) {
            $this->_cache->save($this->_config, 'config');
        }
    }
}