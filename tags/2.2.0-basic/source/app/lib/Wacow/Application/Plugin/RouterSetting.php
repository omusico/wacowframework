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
 * @version    $Id: RouterSetting.php 573 2008-10-08 06:43:50Z jaceju $
 */

/**
 * @see Wacow_Application_Plugin_Abstract
 */
require_once 'Wacow/Application/Plugin/Abstract.php';

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * Wacow_Application_Plugin_RouterSetting
 *
 * @category   Wacow
 * @package    Wacow_Application
 * @subpackage Wacow_Application_Plugin
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Application_Plugin_RouterSetting extends Wacow_Application_Plugin_Abstract
{
    /**
     * Set router config
     *
     */
    public function beforeRun()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();

        if (isset($this->_app->routeConfig->routes)) {
            $router->addConfig($this->_app->routeConfig->routes, 'routes');
        } elseif (isset($this->_app->config->common->routes)) {
            $router->addConfig($this->_app->config->common, 'routes');
        }

        $frontController->setRouter($router);
        return $this;
    }
}