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
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Plugin
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: PathHandler.php 660 2009-04-02 12:24:26Z jaceju $
 */

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Set fulll path of current module for path translator
 *
 * @category   Wacow
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Plugin
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Controller_Plugin_PathHandler extends Zend_Controller_Plugin_Abstract
{
    /**
     * Add Module name traslation to translator.
     *
     * @param Wacow_Controller_Action $actionController
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup()
    {
        $translator = Wacow_Application::getPathTranslator();
        $translator->addPathMapping(':moduleName', $this->_request->getModuleName());
        $translator->addPathMapping(':controllerName', $this->_request->getControllerName());
        $translator->addPathMapping(':actionName', $this->_request->getActionName());
        $translator->addPathMapping(':moduleDir', $translator->getRuntimePath(':modulePath') . '/' . $this->_request->getModuleName());
    }
}