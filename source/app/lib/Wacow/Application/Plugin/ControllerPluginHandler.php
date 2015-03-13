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
 * Wacow_Application_Plugin_ControllerPluginHandler
 *
 * @category   Wacow
 * @package    Wacow_Application
 * @subpackage Wacow_Application_Plugin
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Application_Plugin_ControllerPluginHandler extends Wacow_Application_Plugin_Abstract
{
    /**
     * Register front controller plugin
     *
     */
    public function beforeRun()
    {
        $runtimeMode = $this->_app->runtimeMode;

        if ('http' !== $runtimeMode) {
            return;
        }

        $viewConfig = array();

        if (isset($this->_app->config->common->view)) {
            $viewConfig = $this->_app->config->common->view->toArray();
        }

        $viewOptions = array();
        if (array_key_exists($runtimeMode, $viewConfig)) {
            $viewOptions = $viewConfig[$runtimeMode];
        }

        // register plugins
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin(new Wacow_Controller_Plugin_PathHandler());
        $frontController->registerPlugin(new Wacow_Controller_Plugin_ViewFactory($viewOptions));

        $aclObject = $this->_app->getAcl();
        if ($this->_app->isAclEnabled() && $aclObject) {
            $frontController->registerPlugin(new Wacow_Controller_Plugin_Auth($aclObject, $this->_app->getAclOptions()));
        }
    }
}