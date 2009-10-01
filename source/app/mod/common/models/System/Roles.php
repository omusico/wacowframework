<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @package     Default_Model
 * @copyright
 * @version     $Id$
 */

/**
 * @see App_Db_Table
 */
require_once 'App/Db/Table.php';

/**
 * %Description%
 *
 * @category    %ProjectName%
 * @package     Common_Model
 * @copyright
 */
class System_Roles extends App_Db_Table
{
    /**
     * Table Name
     *
     * @var string
     */
    protected $_name = 'system_roles';

    /**
     * Row Class
     *
     * @var string
     */
    // protected $_rowClass = 'System_Role';

    /**
     * Reference Mapping
     *
     * @var array
     */
    /*
    protected $_referenceMap    = array(
        'rulename' => array(
            'columns'           => 'foreignkey',
            'refTableClass'     => 'tableclassname',
            'refColumns'        => 'id',
        ),
    );
    */

    /**
     * Dependented Tables
     *
     * @var array
     */
    // protected $_dependentTables = array('tablename');

    /**
     * self instance
     *
     * @var System_Roles
     */
    private static $_instance = null;

    /**
     * Get instance
     *
     * @return System_Roles
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Fetch actived roles
     *
     * @return Wacow_Db_Table_Rowset
     */
    public static function fetchActivedRowset()
    {
        $roleTable = self::getInstance();
        $select = $roleTable->select()
                            ->where('status = ?', 'y');
        return $roleTable->fetchAll($select);
    }
}