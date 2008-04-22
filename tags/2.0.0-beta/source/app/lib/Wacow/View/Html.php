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
 * @version    $Id: Html.php 406 2008-04-20 02:43:18Z jaceju $
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
    protected $_frontendVars = null;

    /**
     * Factory method
     *
     * @param array $options
     * @return Wacow_View_Html
     */
    public function factory($options)
    {
        if (array_key_exists('engine', $options) && is_string($options['engine'])) {
            $className = 'Wacow_View_Html_' . ucfirst(strtolower(trim($options['engine'])));
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
     * Set variables for frontend.
     *
     * @param Zend_Controller_Request $request
     */
    public function setFrontendVars(Zend_Controller_Request_Http $request)
    {
        if (!$this->_frontendVars) {
            $app = Wacow_Application::getInstance();
            $this->_frontendVars = array(
                'constants'         => $app->getConstants(),
                'baseUrl'           => $request->getBaseUrl(),
                'moduleName'        => $request->getModuleName(),
                'controllerName'    => $request->getControllerName(),
                'actionName'        => $request->getActionName(),
                'currentUrl'        => $request->getRequestUri(),
            );
        }
        $this->frontendVars = $this->_frontendVars;
    }
}