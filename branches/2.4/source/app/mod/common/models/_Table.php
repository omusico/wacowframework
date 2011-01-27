<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @package     Common_Model
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
class classname extends App_Db_Table
{
    /**
     * Table Name
     *
     * @var string
     */
    protected $_name = 'tablename';

    /**
     * Row Class
     *
     * @var string
     */
    // protected $_rowClass = 'rowclass';

    /**
     * Reference Mapping
     *
     * @var array
     */
    // protected $_referenceMap    = array(
    //     'rulename' => array(
    //         'columns'           => 'foreignkey',
    //         'refTableClass'     => 'tableclassname',
    //         'refColumns'        => 'id',
    //     ),
    // );

    /**
     * Dependented Tables
     *
     * @var array
     */
    // protected $_dependentTables = array('tablename');

    /**
     * self instance
     *
     * @var classname
     */
    private static $_instance = null;

    /**
     * Get instance
     *
     * @return classname
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}