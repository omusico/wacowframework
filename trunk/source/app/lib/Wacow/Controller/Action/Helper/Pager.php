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
 * @version    $Id: Pager.php 580 2008-10-20 02:19:24Z jaceju $
 */

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Pager
 *
 * @category   Wacow
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Action_Helper
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Controller_Action_Helper_Pager extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Total size of dataset
     *
     * @var int
     */
    protected $_totalSize   = 0;

    /**
     * Dataset size per page
     *
     * @var int
     */
    protected $_sizePerPage    = 1;

    /**
     * Current page number
     *
     * @var int
     */
    protected $_currentPage = 1;

    /**
     * Total pages
     *
     * @var int
     */
    protected $_totalPages  = 1;

    /**
     * Offset of dataset
     *
     * @var int
     */
    protected $_offset      = 0;

    /**
     * List of page
     *
     * @var array
     */
    protected $_pageList    = array();

    /**
     * Page size per layer
     *
     * @var int
     */
    private $_pagesPerLayer = 10;

    /**
     * Total layers
     *
     * @var int
     */
    protected $_totalLayers = 1;

    /**
     * Current layer number
     *
     * @var int
     */
    private $_currentLayer = 1;

    /**
     * Name of page param
     *
     * @var string
     */
    private $_paramName = 'page';

    /**
     * Build page with setting
     *
     * Allowed variables are:
     * - paramName - url param name for current page
     * - sizePerPage - data size per page
     * - pagesPerLayer - pages number per layer
     * - totalSize - total data size
     *
     * @param array $setting
     * @return void
     */
    public function buildPage($setting)
    {
        $this->_setSetting($setting);
        $this->_totalPages  = (int) ceil($this->_totalSize / $this->_sizePerPage);
        $this->_totalLayers = (int) ceil($this->_totalPages / $this->_pagesPerLayer);
        if (1 > $this->_totalPages) {
            $this->_totalPages = 1;
        }
        if ($this->_currentPage > $this->_totalPages) {
            $this->_currentPage = $this->_totalPages;
        }
        $this->_buildPageList();
        $this->_offset = ($this->_currentPage - 1) * $this->_sizePerPage;
    }

    /**
     * Get size per page
     *
     */
    public function getPageSize()
    {
        return $this->_sizePerPage;
    }

    /**
     * Get current page number
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->_currentPage;
    }

    /**
     * Rebuild link for page
     *
     * @param string $href
     * @param int $page
     */
    public function buildLink($pageNum, $anchor = '')
    {
        $href = $this->getActionController()->view->url(array($this->_paramName => $pageNum));
        return ('' !== $anchor) ? ($href . '#' . $anchor) : $href;
    }

    /**
     * Get Param name
     *
     * @return string
     */
    public function getParamName()
    {
        return $this->_paramName;
    }

    /**
     * Get total pages
     *
     * @return int
     */
    public function getTotalPages()
    {
        return $this->_totalPages;
    }

    /**
     * Get the offset of page
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * Get information of pager
     *
     * @return array
     */
    public function getInfo()
    {
        $startPos = ($this->_totalSize > 0)
                  ? $this->_offset + 1
                  : 0;
        $endPos   = ($this->_totalSize >= ($tmp = $this->_offset + $this->_sizePerPage))
                  ? $tmp
                  : $this->_totalSize;
        $prevPage = (1 <= ($_prevPage = ($this->_currentPage - 1)))
                  ? $_prevPage
                  : null;
        $nextPage = ($this->_totalPages >= ($_nextPage = ($this->_currentPage + 1)))
                  ? $_nextPage
                  : null;
        $pagePrevLayer = (1 < $this->_currentLayer)
                       ? ($this->_currentLayer - 1) * $this->_pagesPerLayer
                       : null;
        $pageNextLayer = ($this->_totalLayers > $this->_currentLayer)
                       ? ($this->_currentLayer * $this->_pagesPerLayer) + 1
                       : null;
        return array(
            'totalPages'    => $this->_totalPages,
            'totalSize'     => $this->_totalSize,
            'totalLayers'   => $this->_totalLayers,
            'sizePerPage'   => $this->_sizePerPage,
            'startPos'      => $startPos,
            'endPos'        => $endPos,
            'startPage'     => 1,
            'endPage'       => $this->_totalPages,
            'pageList'      => $this->_pageList,
            'currentPage'   => $this->_currentPage,
            'currentLayer'  => $this->_currentLayer,
            'pagesPerLayer' => $this->_pagesPerLayer,
            'prevPage'      => $prevPage,
            'nextPage'      => $nextPage,
            'pagePrevLayer' => $pagePrevLayer,
            'pageNextLayer' => $pageNextLayer,
        );
    }

    /**
     * Set setting
     *
     * @param array $setting
     */
    protected function _setSetting(array $setting)
    {
        if (isset($setting['paramName'])) {
            $this->_paramName = trim($setting['paramName']);
        }
        $setting['currentPage'] = ($p = (int) $this->getRequest()->getParam($this->_paramName)) ? $p : 1;

        if (isset($setting['sizePerPage'])) {
            $this->_sizePerPage = (1 < ($tmp = (int) $setting['sizePerPage']))
                                ? $tmp
                                : 1;
        }

        if (isset($setting['pagesPerLayer'])) {
            $this->_pagesPerLayer = (int) $setting['pagesPerLayer'];
        }
        if (isset($setting['totalSize'])) {
            $tmp = (int) $setting['totalSize'];
            $this->_totalSize = ($tmp > 0) ? $tmp : 0;
        }
        // must be set after pagesPerLayer
        if (isset($setting['currentPage'])) {
            $this->_setCurrentPage((int) $setting['currentPage']);
        }
    }

    /**
     * Set current page number
     *
     * @param int $currentPage
     * @return Wacow_DataSet_Pager_Abstract
     */
    protected function _setCurrentPage($currentPage)
    {
        $tmp = (int) $currentPage;
        $this->_currentPage = ($currentPage > 1) ? $currentPage : 1;
        $this->_currentLayer = (int) ceil($this->_currentPage / $this->_pagesPerLayer);
        return $this;
    }

    /**
     * Build list of page
     *
     * @return void
     */
    protected function _buildPageList()
    {
        $startPage = (1 < ($_s = ($this->_currentLayer - 1) * $this->_pagesPerLayer + 1))
                   ? $_s
                   : 1;
        $endPage   = ($this->_totalPages > ($_e = ($this->_currentLayer * $this->_pagesPerLayer)))
                   ? $_e
                   : $this->_totalPages;

        for ($i = $startPage; $i <= $endPage; $i ++) {
            $this->_pageList[$i] = ($i == $this->_currentPage)
                                ? 'current'
                                : '';
        }
    }
}