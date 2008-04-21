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
 * @version    $Id$
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
     * Fetches a new blank row (not from the database).
     * Fix the result row without default value.
     * Remove the method until the ZF fixed the problem.
     *
     * @param  array $data OPTIONAL data to populate in the new row.
     * @return Wacow_Db_Table_Row
     */
    public function createRow(array $data = array())
    {
        $defaults = array_combine($this->_cols, array_fill(0, count($this->_cols), null));
        foreach ($this->_cols as $col) {
        	if (isset($this->_metadata[$col]['DEFAULT'])) {
        		$defaults[$col] = $this->_metadata[$col]['DEFAULT'];
        	}
        }

        $keys = array_flip($this->_cols);
        $data = array_intersect_key($data, $keys);
        $data = array_merge($defaults, $data);

        $config = array(
            'table'   => $this,
            'data'    => $data,
            'stored'  => false
        );

        Zend_Loader::loadClass($this->_rowClass);
        return new $this->_rowClass($config);
    }

    /**
     * Fetch the total count of the table with specific conditions.
     *
     * @param mixed $where
     * @param int $count
     * @param int $offset
     */
    public function getCount($where = null)
    {
        $column = $this->_primary ? $this->_db->quoteIdentifier($this->_primary) : '*';
        $select = $this->_db->select();
        $select->from($this->_name, array('total' => 'COUNT(' . $column . ')'));
        $where = (array) $where;
        foreach ($where as $key => $val) {
            if (is_int($key)) {
                $select->where($val);
            } else {
                $select->where($key, $val);
            }
        }
        return (int) $this->_db->fetchOne($select);
    }

    /**
     * Get the last query of table
     *
     */
    public function getLastQuery()
    {
        $profile = $this->_db->getProfiler()->getLastQueryProfile();
        return $profile
             ? $profile->getQuery()
             : 'Database Profiler is disabled.';
    }
}