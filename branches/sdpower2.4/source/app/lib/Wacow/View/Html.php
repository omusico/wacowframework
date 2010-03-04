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
 * @package    Wacow_View
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Html.php 608 2008-12-25 04:17:55Z jaceju $
 */

/**
 * @see Wacow_View
 */
require_once 'Wacow/View.php';

/**
 * Concrete class for handling view scripts.
 *
 * @category   Wacow
 * @package    Wacow_View
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_View_Html extends Wacow_View
{
    /**
     * Variables for frontend
     *
     * @var array
     */
    protected $_frontendVars = array();

    /**
     * Factory method
     *
     * @param array $options
     * @return Wacow_View_Html
     */
    public function factory($options)
    {
        if (array_key_exists('engine', $options) && is_string($options['engine'])) {
            $engineName = ucfirst(strtolower(trim($options['engine'])));
            $className = 'Wacow_View_Html_' . $engineName;
            require_once 'Wacow/View/Html/' . $engineName . '.php';
            return new $className($options);
        } else {
            return new Wacow_View_Html($options);
        }
    }

    /**
     * Includes the view script in a scope with only public $this variables.
     *
     * @param string The view script to execute.
     */
    protected function _run()
    {
        include func_get_arg(0);
    }

    /**
     * Set content of layout
     *
     * @param array $request
     */
    public function setLayoutContents(array $layoutContents)
    {
        $this->layoutContents = $layoutContents;
    }

    /**
     * Assign frontend variables.
     *
     * @param string $name
     * @param mixed $value
     */
    public function assignFrontendVar($name, $value)
    {
        $this->setFrontendVars();
        $this->frontendVars[$name] = $value;
    }

    /**
     * Set variables for frontend.
     *
     * @param Zend_Controller_Request $request
     */
    public function setFrontendVars()
    {
        if (!isset($this->frontendVars)) {
            $this->frontendVars = array();
        }

        if (0 === count($this->_frontendVars)) {
            $app = Wacow_Application::getInstance();
            $frontendController = $app->getFrontController();
            $request = $frontendController->getRequest();
            $this->_frontendVars = array(
                'constants'         => $app->getConstants(),
                'baseUrl'           => $request->getBaseUrl(),
                'moduleName'        => $request->getModuleName(),
                'controllerName'    => $request->getControllerName(),
                'actionName'        => $request->getActionName(),
                'currentUrl'        => $request->getRequestUri(),
            );
            $this->frontendVars = $this->_frontendVars;
        }
    }
}