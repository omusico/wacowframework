<?php

/**
 * 可分頁物件介面
 *
 * @category   Wacow
 * @package    Wacow_Pagination
 * @copyright  Copyright (c) 2007-2009 Wacow Inc. (http://www.wabow.com)
 * @author     Jace Ju
 * @license    None
 */
interface Wacow_Pagination_Pagable_Interface
{
    /**
     * 設定分頁器
     *
     * @param Wacow_Pagination_Pager_Abstract $pager
     */
    public function setPager(Wacow_Pagination_Pager_Abstract $pager);

    /**
     * 取得總筆數
     *
     */
    public function getTotalSize();

    /**
     * 設定分頁範圍
     *
     * @param int $offset 起始偏移量
     * @param int $count 資料筆數
     */
    public function setPageRange($offset, $count);
}
