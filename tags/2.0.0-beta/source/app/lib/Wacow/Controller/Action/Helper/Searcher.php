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
 * @subpackage Wacow_Controller_Action
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Searcher.php 406 2008-04-20 02:43:18Z jaceju $
 */

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Build paramaters for search and redirect to result url
 *
 * @category   Wacow
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Action
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Controller_Action_Helper_Searcher extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Unprocessed parameters for search
     *
     * @var array
     */
    protected $_unprocessedParmas = array();

    /**
     * Parameters for search
     *
     * @var array
     */
    protected $_searchParams = array();

    /**
     * Add Column
     *
     * @param string $name
     * @param string $type
     * @return Wacow_Controller_Action_Helper_Searcher
     */
    public function addParam($name, $type, $defaultValue = null)
    {
        $type = strtolower($type);
        if (!in_array($type, array('int', 'string'))) {
            $type = 'string';
        }

        if (!isset($this->_unprocessedParmas[$type])) {
            $this->_unprocessedParmas[$type] = array();
        }

        $this->_unprocessedParmas[$type][$name] = $defaultValue;

        return $this;
    }

    /**
     * Build the parameters for search
     *
     * @return Wacow_Controller_Action_Helper_Searcher
     */
    public function buildSearchParams()
    {
        $request = $this->getRequest();
        $action  = $request->getActionName();
        $this->_processParamsByRequestType($request->getParams());
        if ($request->isPost()) {
            $this->_processParamsByRequestType($request->getPost());

            // Redirect with parameters for search
            $params = array();
            foreach ($this->_searchParams as $name => $value) {
            	if ('' != trim($value)) {
            	    $params[$name] = urlencode($value);
            	}
            }
            $redirector = $this->_actionController->redirector;
            $redirector->goto($action, null, null, $params);
        }
        return $this;
    }

    /**
     * Process parameters by type of request
     *
     * @param array $requestParams
     */
    protected function _processParamsByRequestType($requestParams)
    {
        foreach ($this->_unprocessedParmas as $type => $params) {
            $this->_processParamsByValueType($type, $params, $requestParams);
        }
    }

    /**
     * Process parameters by type of value
     *
     * @param string $type
     * @param array $colnums
     */
    protected function _processParamsByValueType($type, $params, $requestParams)
    {
        foreach ($params as $name => $defaultValue) {
            $value = isset($requestParams[$name])
                   ? trim($requestParams[$name])
                   : $defaultValue;
            if ('int' == $type && preg_match('/^\d+$/', $value)) {
                $this->_searchParams[$name] = (int) $value;
            } else {
                $this->_searchParams[$name] = $value;
            }
        }
    }

    /**
     * Get parameters for search
     *
     * @return array
     */
    public function getSearchParams()
    {
        return $this->_searchParams;
    }
}