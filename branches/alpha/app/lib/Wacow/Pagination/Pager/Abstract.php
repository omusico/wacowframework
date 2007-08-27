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
 * @package    Wacow_Pagination
 * @copyright
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * The abstract class for pager
 *
 * @category   Wacow
 * @package    Wacow_Pagination
 * @copyright
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Wacow_Pagination_Pager_Abstract
{
    /**
     * Total size of dataset
     *
     * @var int
     */
    protected $totalSize   = 0;

    /**
     * Dataset size per page
     *
     * @var int
     */
    protected $pageSize    = 1;

    /**
     * Current page number
     *
     * @var int
     */
    protected $currentPage = 1;

    /**
     * Total pages
     *
     * @var int
     */
    protected $totalPages  = 1;

    /**
     * Offset of dataset
     *
     * @var int
     */
    protected $offset      = 0;

    /**
     * List of page
     *
     * @var array
     */
    protected $pageList    = array();

    /**
     * Constructor
     *
     * @param array $setting
     */
    public function __construct($setting = array())
    {
        if (isset($setting['currentPage'])) {
            $this->setCurrentPage((int) $setting['currentPage']);
        }
        if (isset($setting['pageSize'])) {
            $this->setPageSize((int) $setting['pageSize']);
        }
    }

    /**
     * Set the pagable object
     *
     * @param Wacow_Pagination_Pagable_Interface $pagable
     * @return void
     */
    public function setPagable(Wacow_Pagination_Pagable_Interface $pagable)
    {
        $this->setTotalSize($pagable->getTotalSize());
        $pagable->setPageRange($this->getOffset(), $this->pageSize);
        $this->buildPageList();
    }

    /**
     * Set the size per page
     *
     * @param int $pageSize
     * @return Wacow_Pagination_Pager_Abstract
     */
    public function setPageSize($pageSize)
    {
        $tmp = (int) $pageSize;
        $this->pageSize = ($tmp > 1) ? $tmp : 1;
        return $this;
    }

    /**
     * Set current page number
     *
     * @param int $currentPage
     * @return Wacow_Pagination_Pager_Abstract
     */
    public function setCurrentPage($currentPage)
    {
        $tmp = (int) $currentPage;
        $this->currentPage = ($currentPage > 1) ? $currentPage : 1;
        return $this;
    }

    /**
     * Get current page number
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Get total pages
     *
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * Get information of pager
     *
     * @return array
     */
    public function getInfo()
    {
        $startPos = ($this->totalSize > 0)
                  ? $this->offset + 1
                  : 0;
        $endPos   = ($this->totalSize >= ($tmp = $this->offset + $this->pageSize))
                  ? $tmp
                  : $this->totalSize;
        return array(
            'totalPages'  => $this->totalPages,
            'totalSize'   => $this->totalSize,
            'pageSize'    => $this->pageSize,
            'startPos'    => $startPos,
            'endPos'      => $endPos,
            'startPage'   => 1,
            'endPage'     => $this->totalPages,
            'pageList'    => $this->pageList,
            'currentPage' => $this->currentPage
        );
    }

    /**
     * Build list of page
     *
     * @return void
     */
    protected function buildPageList()
    {
        for ($i = 1; $i <= $this->totalPages; $i ++) {
            $this->pageList[$i] = ($i == $this->currentPage)
                                ? 'current'
                                : '';
        }
    }

    /**
     * Set total size of dataset
     *
     * @param int $totalSize
     * @return Wacow_Pagination_Pager_Abstract
     */
    protected function setTotalSize($totalSize)
    {
        $tmp = (int) $totalSize;
        $this->totalSize = ($tmp > 0) ? $tmp : 0;
        return $this;
    }

    /**
     * Get the offset of page
     *
     * @return int
     */
    protected function getOffset()
    {
        $this->totalPages = (int) ceil($this->totalSize / $this->pageSize);
        if (1 > $this->totalPages) {
            $this->totalPages = 1;
        }
        if ($this->currentPage > $this->totalPages) {
            $this->currentPage = $this->totalPages;
        }
        return $this->offset = ($this->currentPage - 1) * $this->pageSize;
    }
}
