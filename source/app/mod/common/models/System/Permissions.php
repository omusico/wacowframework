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
class System_Permissions extends App_Db_Table
{
    /**
     * Table Name
     *
     * @var string
     */
    protected $_name = 'system_permissions';

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
    protected $_referenceMap    = array(
        'Role' => array(
            'columns'           => 'roleId',
            'refTableClass'     => 'System_Roles',
            'refColumns'        => 'id',
        ),
        'Resource' => array(
            'columns'           => 'resourceId',
            'refTableClass'     => 'System_Resources',
            'refColumns'        => 'id',
        ),
    );

    /**
     * Dependented Tables
     *
     * @var array
     */
    // protected $_dependentTables = array('tablename');

    /**
     * self instance
     *
     * @var System_Permissions
     */
    private static $_instance = null;

    /**
     * Get instance
     *
     * @return System_Permissions
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Fetch resource list by roleId
     *
     */
    public function fetchResourceIdListByRoleId($roleId)
    {
        $roleId = (int) $roleId;
        $permissionTable = System_Permissions::getInstance();
        $select = $permissionTable->select()
                                  ->where('roleId = ?', $roleId)
                                  ->where('status = ?', 'y');
        return $permissionTable->fetchCol('resourceId', $select);
    }

    /**
     * Get resource list by roleId
     *
     * @param int $roleId
     * @return array
     */
    public function getResourceListByRoleId($roleId)
    {
        $resourceIdList = System_Permissions::fetchResourceIdListByRoleId($roleId);

        // Get resource
        $resourceRowset = System_Resources::fetchActivedRowset(null);

        // Build resource
        $resourceList = array();
        foreach ($resourceRowset as $resourceRow) {
            $allowed = false;
            if (in_array((int) $resourceRow->id, $resourceIdList)) {
                $allowed = true;
            }
            if ($resourceRow->parentId) {
                $resourceList[$resourceRow->parentId]['subResourceList'][$resourceRow->id] = array_merge(
                    $resourceRow->toArray(),
                    array(
                        'allowed' => $allowed,
                    )
                );
            } else {
                $resourceList[$resourceRow->id] = array_merge(
                    $resourceRow->toArray(),
                    array(
                        'allowed' => $allowed,
                        'subResourceList' => array(),
                    )
                );
            }

        }
        return $resourceList;
    }
}