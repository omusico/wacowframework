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
 * @version    $Id: Searcher.php 619 2009-01-08 04:57:33Z jaceju $
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
 * @subpackage Wacow_Controller_Action_Helper
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
     * Anchor of url
     *
     * @var string
     */
    protected $_anchor = '';

    /**
     * Special Chars
     *
     * @var array
     */
    protected $_specialChars = array(
        '/' => '%F2F',
        ' ' => '%F20',
    );

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
     * Set anchor of url
     *
     * @param string $anchor
     */
    public function setAnchor($anchor)
    {
        $this->_anchor = trim($anchor);
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
                $search  = array_keys($this->_specialChars);
                $replace = array_values($this->_specialChars);
                $value = str_replace($search, $replace, $value);

            	if ('' != trim($value)) {
            	    $params[$name] = $value;
            	}
            }
            $redirector = $this->_actionController->redirector;
            /* @var $redirector Zend_Controller_Action_Helper_Redirector */
            $redirector->setGotoSimple($action, null, null, $params);
            $url = $redirector->getRedirectUrl() . (('' !== $this->_anchor) ? '#' : '') . $this->_anchor;
            $redirector->gotoUrlAndExit($url, array('prependBase' => false));
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
                $search  = array_values($this->_specialChars);
                $replace = array_keys($this->_specialChars);
                $value = str_replace($search, $replace, $value);
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