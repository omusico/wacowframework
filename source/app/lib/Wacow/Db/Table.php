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
 * @package    Wacow_Db
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Table.php 700 2009-09-03 07:37:20Z jaceju $
 */

/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * Database table class
 *
 * @category   Wacow
 * @package    Wacow_Db
 * @subpackage Wacow_Db_Table
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Db_Table extends Zend_Db_Table_Abstract
{
    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'Wacow_Db_Table_Rowset';

    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Wacow_Db_Table_Row';

    /**
     * Get primary key
     *
     * @return mixed
     */
    public function getPrimary()
    {
        return $this->_primary;
    }

    /**
     * Fetches a new blank row (not from the database).
     *
     * @param  array $data OPTIONAL data to populate in the new row.
     * @param  string $defaultSource OPTIONAL flag to force default values into new row
     * @return Wacow_Db_Table_Row
     */
    public function createRow(array $data = array(), $defaultSource = Zend_Db_Table_Abstract::DEFAULT_DB)
    {
        return parent::createRow($data, $defaultSource);
    }

    /**
     * Fetch the total count of the table with specific conditions.
     *
     * @param mixed $where
     * @param int $count
     * @param int $offset
     */
    public function getCount($where = null, $count = null, $offset = null)
    {
        if (!($where instanceof Zend_Db_Table_Select)) {
            return (int) $this->fetchOne(array('total' => 'COUNT(*)'), $where, $count, $offset);
        } else {
            $select = clone $where; /* @var $select Zend_Db_Select */
            $select->reset(Zend_Db_Select::ORDER);
            $pattern = '/^SELECT (?!FROM).+ (FROM .+)$/iU';
            $sql = preg_replace('/\s/', ' ', $select->__toString());
            $result = preg_match($pattern, $sql, $matches);
            if (isset($matches[1])) {
                $sql = 'SELECT COUNT(*) ' . $matches[1];
                return (int) $this->_db->fetchOne($sql);
            } else {
                return 0;
            }
        }
    }

    /**
     * Get the last query of table
     *
     * @return string
     */
    public function getLastQuery()
    {
        $profile = $this->_db->getProfiler()->getLastQueryProfile();
        return $profile
             ? $profile->getQuery()
             : 'Database Profiler is disabled.';
    }

    /**
     * Batch quoteInto
     *
     * @param array $conditionList
     * @return array
     */
    public function batchQuoteInto(array $conditionList)
    {
        $where = array();
        foreach ($conditionList as $condition => $value) {
            $where[] = $this->getAdapter()->quoteInto($condition, $value);
        }
        return $where;
    }

    /**
     * Fetches key => values pairs.
     *
     * Honors the Zend_Db_Adapter fetch mode.
     *
     * @param array                             $columns    Array of columns, this must be 2 columns.
     * @param string|array|Zend_Db_Table_Select $where      OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order      OPTIONAL An SQL ORDER clause.
     * @param int                               $count      OPTIONAL An SQL LIMIT count.
     * @param int                               $offset     OPTIONAL An SQL LIMIT offset.
     * @return Zend_Db_Table_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
     * @author Amr Mostafa
     */
    public function fetchPairs(array $columns, $where = null, $order = null, $count = null, $offset = null)
    {
        if (count($columns) > 2) {
            require_once "Zend/Db/Table/Exception.php";
            throw new Zend_Db_Table_Exception("Cannot build key => value pairs of more than exactly 2 columns");
        }

        $select = $this->selectColumns($columns, $where, $order, $count, $offset);

        return $this->getAdapter()->fetchPairs($select);
    }

    /**
     * Fetches the first column of the first row.
     *
     * Honors the Zend_Db_Adapter fetch mode.
     *
     * @param array                             $column     Array of columns, this must be 2 columns.
     * @param string|array|Zend_Db_Table_Select $where      OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order      OPTIONAL An SQL ORDER clause.
     * @param int count
     * @param int offset
     * @return Zend_Db_Table_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
     */
    public function fetchOne($column, $where = null, $order = null, $count = null, $offset = null)
    {
        $select = $this->selectColumns($column, $where, $order, $count, $offset);
        return $this->getAdapter()->fetchOne($select);
    }

    /**
     * Fetches the first column of all SQL result rows as an array.
     *
     * The first column in each row is used as the array key.
     *
     * @param string $column
     * @param string|array|Zend_Db_Table_Select $where      OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order      OPTIONAL An SQL ORDER clause.
     * @param int count
     * @param int offset
     * @return array
     */
    public function fetchCol($column, $where = null, $order = null, $count = null, $offset = null)
    {
        $select = $this->selectColumns($column, $where, $order, $count, $offset);
        return $this->getAdapter()->fetchCol($select);
    }

    /**
     * Select the specific columns
     *
     * @param array                             $columns    Specific columns
     * @param string|array|Zend_Db_Table_Select $where      OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order      OPTIONAL An SQL ORDER clause.
     * @param int                               $count      OPTIONAL An SQL LIMIT count.
     * @param int                               $offset     OPTIONAL An SQL LIMIT offset.
     * @return Zend_Db_Table_Select
     */
    public function selectColumns($columns, $where = null, $order = null, $count = null, $offset = null)
    {
        $columns = (array) $columns;

        if (!($where instanceof Zend_Db_Table_Select)) {
            $select = $this->select()->from($this, $columns);

            if ($where !== null) {
                $this->_where($select, $where);
            }

            if ($order !== null) {
                $this->_order($select, $order);
            }

            if ($count !== null || $offset !== null) {
                $select->limit($count, $offset);
            }

        } else {

            $select = clone $where; /* @var $select Zend_Db_Table_Select */

            $select->reset(Zend_Db_Select::FROM)
                   ->reset(Zend_Db_Select::COLUMNS);

            $select->from($this, $columns);

            if ($order !== null) {
                $select->order($order);
            }

            if ($count !== null || $offset !== null) {
                $select->limit($count, $offest);
            }
        }

        return $select;
    }
}