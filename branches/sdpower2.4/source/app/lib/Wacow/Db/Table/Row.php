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
 * @version    $Id: Row.php 480 2008-05-22 08:51:15Z jaceju $
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
     * Db options from config
     *
     * @var Zend_Config
     */
    protected $_dbOptions = null;

    /**
     * Constructor.
     *
     * Supported params for $config are:-
     * - table       = class name or object of type Zend_Db_Table_Abstract
     * - data        = values of columns in this row.
     *
     * @param  array $config OPTIONAL Array of user-specified config options.
     * @return void
     * @throws Zend_Db_Table_Row_Exception
     */
    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_dbOptions = Wacow_Application::getInstance()->getDbOptions();
    }

    /**
     * Get database adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDbAdapter()
    {
        return $this->_table->getAdapter();
    }

    /**
     * Get the last query of row
     *
     * @return string
     */
    public function getLastQuery()
    {
        return $this->_table->getLastQuery();
    }
}