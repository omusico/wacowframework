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
 * @subpackage Wacow_Controller_Action_Helper
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: LayoutManager.php 669 2009-04-10 05:01:12Z jaceju $
 */

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Layout Manager
 *
 * @category   Wacow
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Action_Helper
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Controller_Action_Helper_LayoutManager extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Use layout
     *
     * @var boolean
     */
    private $_layoutEnabled = false;

    /**
     * Name of main template (without extension)
     *
     * @var string
     */
    private $_layoutName = null;

    /**
     * Name of dynamic content
     *
     * @var string
     */
    private $_defaultContentName = 'contentFromAction';

    /**
     * Contents for layout
     *
     * @var array
     */
    private $_layoutContents = array();

    /**
     * View object
     *
     * @var Wacow_View
     */
    private $_view = null;

    /**
     * View renderer object
     *
     * @var Zend_Controller_Action_Helper_ViewRenderer
     */
    private $_viewRenderer = null;

    /**
     * Initialize
     *
     * @return void
     */
    public function init()
    {
        // Get view object
        $this->_viewRenderer = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer');
        $this->_view = $this->_viewRenderer->view;
    }

    /**
     * Disable layout
     *
     * @return Wacow_Controller_Action_Helper_LayoutManager
     */
    public function disableLayout()
    {
        $this->_layoutEnabled = false;
        return $this;
    }

    /**
     * Return layout enabled
     *
     * @return string
     */
    public function isLayoutEnabled()
    {
        return $this->_layoutEnabled;
    }

    /**
     * Set name of main template
     *
     * @param string $layoutName
     * @return Wacow_Controller_Action_Helper_LayoutManager
     */
    public function setLayout($layoutName)
    {
        if (0 != strlen($layoutName)) {
            $this->_layoutName = $layoutName;
            $this->_layoutEnabled = true;
        }
        return $this;
    }

    /**
     * Get name of main template
     *
     * @return string
     */
    public function getLayoutName()
    {
        return $this->_layoutName;
    }

    /**
     * set name of dynamic content
     *
     * @param string $name
     * @return Wacow_Controller_Action_Helper_LayoutManager
     */
    public function setContentName($name)
    {
        if (0 != strlen($name)) {
            $this->_defaultContentName = $name;
        }
        return $this;
    }

    /**
     * View initialization
     *
     * @return void
     */
    public function preDispatch()
    {
        $this->_view->setFrontendVars();
    }

    /**
     * Render result
     *
     * @return string
     */
    public function postDispatch()
    {
        $isDispatched = $this->getRequest()->isDispatched();
        $isRedirect   = $this->getResponse()->isRedirect();
        $isNoRender   = !$isDispatched ||
                        $isRedirect ||
                        $this->_viewRenderer->getNeverRender() ||
                        ($this->_viewRenderer->getNoRender() && !$this->_layoutEnabled);

        // Do not want render or dispatch is not finish yet.
        if ($isNoRender) {
            return;
        }

        // We need disable the viewrenderer to avoid render twice
        if ($isDispatched && !$isRedirect && (null !== $this->_actionController)) {
            $this->_viewRenderer->setNoRender();
        }

        $mainTemplate = null;

        // support ajax html output without layout
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_layoutEnabled = false;
        }
        $this->_view->layoutEnabled = $this->_layoutEnabled;

        if ($this->_layoutEnabled) {

            // get filename of main template
            $extension = ($suffix = $this->_viewRenderer->getViewSuffix())
                       ? '.' . $suffix
                       : '';
            $mainTemplate = $this->_layoutName . $extension;

            // process original output
            $viewScript = $this->_viewRenderer->getViewScript();
            $this->_layoutContents[$this->_defaultContentName] = $this->_view->render($viewScript);
        } else {
            $mainTemplate = $viewScript = $this->_viewRenderer->getViewScript();
        }

        /**
         * @todo Refactoring here
         */
        $this->_view->setLayoutContents($this->_layoutContents);
        $this->_view->layoutEnabled = $this->_layoutEnabled = false;

        // process main template
        $result = ($mainTemplate)
                ? $this->_view->render($mainTemplate)
                : null;

        $this->getResponse()->append('LayoutManager', $result);
    }
}