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
 * @version    $Id: ViewFactory.php 673 2009-04-24 08:47:29Z jaceju $
 */

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Build view object
 *
 * @category   Wacow
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Plugin
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Controller_Plugin_ViewFactory extends Zend_Controller_Plugin_Abstract
{
    /**
     * Wacow_Application
     *
     * @var Wacow_Application
     */
    private $_app = null;

    /**
     * options of view object and viewrenderder
     *
     * @var array
     */
    private $_viewOptions = array();

    /**
     * Defined View Type
     *
     * @var array
     */
    private static $_definedViewType = array(
        'html',
        'json',
        'text',
        'xml',
    );

    /**
     * Class constructor
     *
     * @param array $viewOptions
     */
    public function __construct($viewOptions)
    {
        $this->_app = Wacow_Application::getInstance();
        $this->_viewOptions = $viewOptions;
    }

    /**
     * Decide the type of view object
     *
     * If the requrest is got from ajax, then create an Wacow_View_Ajax object for render.
     * Otherwise we use the Wacow_View_Html (Smarty) for render.
     *
     * @param Wacow_Controller_Action $actionController
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // get view type
        $viewType = $this->_getViewType();

        // translate options
        $this->_viewOptions = array_merge(Wacow_View::getStaticOptions(), $this->_viewOptions);
        $this->_viewOptions = Wacow_Application::translatePath($this->_viewOptions);

        // setup view
        $view = Wacow_View::factory($viewType, $this->_viewOptions->toArray());
        /* @var Wacow_View_Html $view */
        // $view->setFrontendVars();
        Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
        Zend_Controller_Action_HelperBroker::addHelper(new Zend_Controller_Action_Helper_ViewRenderer($view, Wacow_View::getStaticOptions()));
        Zend_Controller_Action_HelperBroker::addHelper(new Wacow_Controller_Action_Helper_LayoutManager());
    }

    /**
     * Get View type
     *
     * Auto view type switching by request
     *
     * @return string
     */
    protected function _getViewType()
    {
        $viewType = 'html';
        $responseFormat = strtolower($this->_request->getParam('format'));
        if (!empty($responseFormat)) {
            if (in_array($responseFormat, self::$_definedViewType)) {
                $viewType = $responseFormat;
            }
        } else {
            if ($this->_request->isXmlHttpRequest()) {
                $viewType = 'json';
            }
        }
        return $viewType;
    }
}