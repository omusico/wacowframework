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
 * Wacow_Pagination_Pager_Abstract
 */
require_once 'Wacow/Pagination/Pager/Abstract.php';

/**
 * Slide Pager
 *
 * @category   Wacow
 * @package    Wacow_Pagination
 * @copyright
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Pagination_Pager_Slide extends Wacow_Pagination_Pager_Abstract
{
    /**
     * Page size per layer
     *
     * @var int
     */
    private $layerSize = 10;

    /**
     * Current layer number
     *
     * @var int
     */
    private $currentLayer = 1;

    /**
     * Constructor
     *
     * @param array $setting
     */
    public function __construct(array $setting)
    {
        if (isset($setting['layerSize'])) {
            $this->layerSize = ((int) $setting['layerSize']);
        }
        parent::__construct($setting);
    }

    /**
     * Set current page number
     *
     * @param int $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        parent::setCurrentPage($currentPage);
        $this->currentLayer = ceil($this->currentPage / $this->layerSize);
    }

    /**
     * Get information of pager
     *
     * @return array
     */
    public function getInfo()
    {
        $info = parent::getInfo();
        $info['currentLayer'] = $this->currentLayer;
        $info['layerSize']    = $this->layerSize;
        return $info;
    }

    /**
     * Build list of page
     *
     * @return void
     */
    protected function buildPageList()
    {
        $startPage = (1 <= ($_s = ($this->currentLayer - 1) * $this->layerSize))
                   ? $_s
                   : 1;
        $endPage   = ($this->totalPages >= ($_e = ($this->currentLayer * $this->layerSize + 1)))
                   ? $_e
                   : $this->totalPages;

        for ($i = $startPage; $i <= $endPage; $i ++) {
            $this->pageList[$i] = ($i == $this->currentPage)
                                ? 'current'
                                : '';
        }
    }
}
