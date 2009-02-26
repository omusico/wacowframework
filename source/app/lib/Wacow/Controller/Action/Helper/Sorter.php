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
 * @version    $Id$
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
class Wacow_Controller_Action_Helper_Sorter extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Field for sort
     *
     * @var string
     */
    protected $_fieldName = '';

    /**
     * Order of sort
     *
     * @var string
     */
    protected $_sortDir = 'asc';

    /**
     * Param name for field
     *
     * @var string
     */
    protected $_fieldParamName = 'orderby';

    /**
     * Param name for sort
     *
     * @var string
     */
    protected $_dirParamName = 'sort';

    /**
     * Set setting
     *
     * @param array $setting
     */
    protected function _setSetting(array $setting)
    {
        if (isset($setting['fieldParamName'])) {
            $this->_fieldParamName = trim($setting['fieldParamName']);
        }

        if (isset($setting['dirParamName'])) {
            $this->_dirParamName = trim($setting['dirParamName']);
        }
    }

    public function setDefaultField($field, $dir)
    {
        $this->_fieldName = $field;
        $dir = strtoupper($dir);
        if (in_array($dir, array('ASC', 'DESC'))) {
            $this->_sortDir = $dir;
        }
    }

    /**
     * Get sub SQL
     *
     */
    public function getOrderBy($setting = array())
    {
        $this->_setSetting($setting);
        $request = $this->getRequest();

        if ($f = trim($request->getParam($this->_fieldParamName))) {
            $this->_fieldName = $f;
        }

        if ($s = strtoupper(trim($request->getParam($this->_dirParamName)))) {
            $this->_sortDir = $s;
        }

        return ('' !== $this->_fieldName) ? trim("{$this->_fieldName} {$this->_sortDir}") : '';
    }

    /**
     * Rebuild link for list
     *
     * @param string $href
     * @param int $page
     */
    public function buildLink($fieldName, $anchor = '')
    {
        $sortDir = 'asc';
        if (strtolower($fieldName) === strtolower($this->_fieldName)) {
            $sortDir = ('asc' === strtolower($this->_sortDir))
                     ? 'desc' : 'asc';
        }

        $params = array(
            $this->_fieldParamName => $fieldName,
            $this->_dirParamName => $sortDir,
        );
        $href = $this->getActionController()->view->url($params);
        return ('' !== $anchor) ? ($href . '#' . $anchor) : $href;
    }

    /**
     * Get Current field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->_fieldName;
    }

    /**
     * Get Sort dir
     *
     * @return string
     */
    public function getSortDirection()
    {
        return strtolower($this->_sortDir);
    }
}