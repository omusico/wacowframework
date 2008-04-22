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
 * @subpackage Wacow_Db_Table
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Row.php 406 2008-04-20 02:43:18Z jaceju $
 */

/**
 * @see Zend_Db_Table_Row
 */
require_once 'Zend/Db/Table/Row/Abstract.php';

/**
 * Database table class
 *
 * @category   Wacow
 * @package    Wacow_Db
 * @subpackage Wacow_Db_Table
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Db_Table_Row extends Zend_Db_Table_Row_Abstract
{
    /**
     * Set create datetime
     *
     * @return void
     */
    protected function _insert()
    {
        if (array_key_exists('createDateTime', $this->_data)) {
            $this->createDateTime = date('Y-m-d H:i:s');
        }
        if (array_key_exists('updateDateTime', $this->_data)) {
            $this->updateDateTime = date('Y-m-d H:i:s');
        }
    }

    /**
     * Set update datetime
     *
     */
    protected function _update()
    {
        if (array_key_exists('updateDateTime', $this->_data)) {
            $this->updateDateTime = date('Y-m-d H:i:s');
        }
    }

    /**
     * Get the last query of row
     */
    public function getLastQuery()
    {
        return $this->_table->getLastQuery();
    }
}