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
class System_Resources extends App_Db_Table
{
    /**
     * Table Name
     *
     * @var string
     */
    protected $_name = 'system_resources';

    /**
     * Row Class
     *
     * @var string
     */
    protected $_rowClass = 'System_Resource';

    /**
     * Reference Mapping
     *
     * @var array
     */
    protected $_referenceMap    = array(
        'Parent' => array(
            'columns'           => 'parentId',
            'refTableClass'     => 'System_Resources',
            'refColumns'        => 'id',
        ),
    );

    /**
     * Dependented Tables
     *
     * @var array
     */
    protected $_dependentTables = array('System_Resources', 'System_Permissions');

    /**
     * self instance
     *
     * @var System_Resources
     */
    private static $_instance = null;

    /**
     * Get instance
     *
     * @return System_Resources
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Fetch rowset grouped by controller
     *
     */
    public static function fetchRowsetGroupedByController()
    {
        $menuTable = self::getInstance();
        $select = $menuTable->select()
                            ->where('status = ?', 'y')
                            ->group('module')
                            ->group('controller');
        return $menuTable->fetchAll($select);
    }

    /**
     * Actived resources
     *
     * @var Wacow_Db_Table_Rowset
     */
    private static $_activedRowset = null;

    /**
     * Fetch actived resources
     *
     * @return Wacow_Db_Table_Rowset
     */
    public static function fetchActivedRowset($isDisplay = 'n', $moduleName = null)
    {
        if (null === self::$_activedRowset) {
            $resourceTable = self::getInstance();
            $select = $resourceTable->select()
                                ->where('status = ?', 'y');
            if ($isDisplay) {
                $select->where('display = ?', $isDisplay);
            }
            if ($moduleName) {
                $select->where('module = ?', (string) $moduleName);
            }
            self::$_activedRowset = $resourceTable->fetchAll($select);
        }
        return self::$_activedRowset;
    }
}