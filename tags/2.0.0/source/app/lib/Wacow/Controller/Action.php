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
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Action.php 406 2008-04-20 02:43:18Z jaceju $
 */

/**
 * @see Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';

/**
 * Action Controller
 *
 * @category   Wacow
 * @package    Wacow_Controller
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Controller_Action extends Zend_Controller_Action
{
    /**
     * Application object
     *
     * @var Wacow_Application
     */
    protected $_app = null;

    /**
     * Base url of current action controller
     *
     * @var string
     */
    protected $_controllerBaseUrl = '';

    /**
     * Default cache settings
     *
     * @var array
     */
    protected static $_cacheSettings = array(
        'frontendType' => 'Core',
        'backendType' => 'File',
        'frontendOptions' => array(
            'lifetime' => 2592000, // cache lifetime of 30 days
            'automatic_serialization' => true
        ),
        'backendOptions' => array(
            'cache_dir' => ':modulePath/:moduleName/caches/formRules/',
        ),
    );

    /**
     * Class constructor
     *
     * @see Zend_Controller_Action::__construct()
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs Any additional invocation arguments
     * @return void
     */
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->_app = Wacow_Application::getInstance();
        parent::__construct($request, $response, $invokeArgs);
    }

    /**
     * Set main layout template when action renders.
     *
     * @param string $layoutName
     * @var Wacow_Controller_Action_Helper_LayoutManger $layoutManager
     * @return void
     */
    public function setLayout($layoutName, $name = 'contentFromAction')
    {
        $this->getHelper('LayoutManager')
             ->setContentName($name)
             ->setLayout($layoutName);
    }

    /**
     * Call this method when action does not use layout.
     *
     * @return void
     */
    public function disableLayout()
    {
        $this->getHelper('LayoutManager')->disableLayout();
    }

    /**
     * Call this method when action does render nothing.
     *
     * @return void
     */
    public function noRender()
    {
        $this->getHelper('LayoutManager')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
    }

    /**
     * Call this method to globally dis/enable autorendering.
     *
     * @return void
     */
    public function neverRender()
    {
        $this->getHelper('ViewRenderer')->setNeverRender();
    }

    /**
     * Get Helper
     *
     * @param string $name
     * @return Zend_Controller_Action_Helper_Abstract
     */
    private function __get($name)
    {
        try {
            return $this->_helper->getHelper($name);
        } catch (Zend_Controller_Action_Exception $e) {
            throw $e;
        }
    }

    /**
     * Initaial base url of current action controller
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return Wacow_Controller_Action
     */
    protected function _setControllerBaseUrl($request)
    {
        $this->_controllerBaseUrl = '';
        if ('default' != $moduleName = $request->getModuleName()) {
            $this->_controllerBaseUrl .= '/' . $moduleName;
        }
        $this->_controllerBaseUrl .= '/' . $request->getControllerName();
    }

    /**
     * Get base url of current action controller
     *
     * @return string
     */
    public function getControllerBaseUrl($addBaseUrl = true)
    {
        return ($addBaseUrl)
             ? $this->_request->getBaseUrl() . $this->_controllerBaseUrl
             : $this->_controllerBaseUrl;
    }

    /**
     * Get current url with params
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return ltrim($this->view->url(), $this->_request->getBaseUrl());
    }

    /**
     * Get render result of template.
     *
     * @param string $templateName
     * @return string
     */
    public function renderTemplate($name = null)
    {
        $viewRenderer = $this->viewRenderer; /* @var $viewRenderer Zend_Controller_Action_Helper_ViewRenderer */
        $this->view->strictVars(true);
        $vars = get_object_vars($this->view);
        $engine = $this->view->getEngine();
        foreach ($vars as $key => $value) {
            if ('_' != substr($key, 0, 1)) {
                $engine->assign($key, $value);
            }
        }

        // If no template name assigned, then use 'controller/action' for default.
        if (null == $name) {
            $request = $this->getRequest();
            $name = $request->getControllerName() . '/' . $request->getActionName();
        }

        return $this->view->fetch($name);
    }

    /**
     * Redirect to action with new params
     *
     * @param string $action
     * @param array $params
     * @param boolean $exit
     */
    protected function _redirectAction($action = 'index', $params = array(), $exit = true)
    {
        if ($this->isAjax()) {
            return null;
        }
        if ($exit) {
            $this->_redirector->gotoAndExit($action, null, null, $params);
        } else {
            $this->_redirector->goto($action, null, null, $params);
        }
    }

    /**
     * Redirect to another URL
     *
     * Proxies to {@link Zend_Controller_Action_Helper_Redirector::gotoUrl()}.
     *
     * @param string $url
     * @param array $options Options to be used when redirecting
     * @return void
     */
    protected function _redirect($url, array $options = array())
    {
        if ($this->isAjax()) {
            return null;
        }
        parent::_redirect($url, $options);
    }

    /**
     * Is a request from ajax?
     *
     * @return boolean
     */
    public function isAjax()
    {
        return (bool) $this->_request->isXmlHttpRequest();
    }

    /**
     * Is post back?
     *
     * @return boolean
     */
    public function isPost()
    {
        return (bool) $this->_request->isPost();
    }
}