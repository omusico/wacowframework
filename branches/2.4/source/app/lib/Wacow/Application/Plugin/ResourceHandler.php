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
 * @version    $Id$
 */

/**
 * @see Wacow_Application_Plugin_Abstract
 */
require_once 'Wacow/Application/Plugin/Abstract.php';

/**
 * Wacow_Application_Plugin_ResourceHandler
 *
 * @category   Wacow
 * @package    Wacow_Application
 * @subpackage Wacow_Application_Plugin
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Application_Plugin_ResourceHandler extends Wacow_Application_Plugin_Abstract
{
    /**
     * Database options of application
     *
     * @var array
     */
    private $_dbOptions = array();

    /**
     * Database adapters
     *
     * @var array
     */
    protected $_dbadapters = array();

    /**
     * SMTP options of application
     *
     * @var array
     */
    private $_smtpOptions = array();

    /**
     * Mailers
     *
     * @var array
     */
    protected $_mailers = array();

    /**
     * Set resource config
     *
     */
    public function beforeRun()
    {
        $this->_dbOptions   = $this->_app->getRuntimeConfig()->database;
        $this->_smtpOptions = $this->_app->getRuntimeConfig()->smtp;
    }

    /**
     * Get named database adapter
     *
     * @param string $name
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDbAdapter($name = 'default')
    {
        if (!isset($this->_dbadapters[$name])) {
            try {
                $this->_dbadapters[$name] = Wacow_Application_Resource::getDbAdapter($this->_dbOptions->$name);
            } catch (Exception $e) {
                throw $e;
            }

            if ('default' === $name) {
                Zend_Db_Table::setDefaultAdapter($this->_dbadapters[$name]);
            }

            if (!$this->_app->debugMode) {
                $metacacheStorage = 'File';
                $metacacheLifetime = null;
                $metacacheOptions = array('cache_dir' => ':cachePath/tablemeta');
                if (isset($this->_dbOptions->$name->metacache)) {
                    $metacacheSettings = $this->_dbOptions->$name->metacache;
                    $metacacheStorage  = isset($metacacheSettings->storage) ? $metacacheSettings->storage : 'File';
                    $metacacheLifetime = isset($metacacheSettings->lifetime) ? (int) $metacacheSettings->lifetime : null;
                    if (isset($metacacheSettings->options)) {
                        $metacacheOptions = $metacacheSettings->options;
                    }
                }

                $setting = array(
                    'frontendName'    => 'Core',
                    'backendName'     => $metacacheStorage,
                    'frontendOptions' => array('lifetime' => $metacacheLifetime, 'automatic_serialization' => true,),
                    'backendOptions'  => Wacow_Application::translatePath($metacacheOptions)->toArray(),
                );
                $cache = Wacow_Application_Resource::getCache($setting);

                Zend_Db_Table::setDefaultMetadataCache($cache);
            }
        }
        return $this->_dbadapters[$name];
    }

    /**
     * Get runtime db options.
     *
     * @param string $name
     * @return Zend_Config
     */
    public function getDbOptions($name = 'default')
    {
        if (isset($this->_dbOptions->$name)) {
            return $this->_dbOptions->$name;
        }
        return null;
    }

    /**
     * Get named mailer
     *
     * @param string $name
     * @return Wacow_Mail
     */
    public function getMailer($name = 'default')
    {
        $type = '';
        try {
            $type = $this->_app->mailer;
        } catch (Exception $e) {
            throw new Wacow_Application_Exception('Please define the mailer at config first.');
        }

        if (!isset($this->_mailers[$name])) {
            $this->_mailers[$name] = Wacow_Application_Resource::getMailer($type, $this->_smtpOptions->$name);
        }
        return $this->_mailers[$name];
    }

    /**
     * Get view
     *
     * @param string $type (html, json...)
     * @return Wacow_View
     */
    public function getView($viewType)
    {
        $runtimeMode = $this->_app->runtimeMode;

        $viewOptions = array(
            'commonTemplateDir' => ':commonPath/views/scripts',
            'compileDir'        => ':tmpPath/compile/:moduleName',
            'cacheDir'          => ':tmpPath/html/:moduleName',
        );

        if (isset($this->_app->config->common->view->$runtimeMode)
                && $this->_app->config->common->view->$runtimeMode instanceof Zend_Config) {
            $viewOptions = $this->_app->config->common->view->$runtimeMode->toArray();
        }

        $viewOptions = array_merge(Wacow_View::getStaticOptions(), $viewOptions);
        $viewOptions = Wacow_Application::translatePath($viewOptions);
        return Wacow_View::factory($viewType, $viewOptions->toArray());
    }
}