<?php
/**
 * %Project Name%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @package     App
 * @copyright
 * @version     $Id: Acl.php 492 2008-05-25 03:47:22Z jaceju $
 */

/**
 * @see Zend_Acl
 */
require_once 'Zend/Acl.php';

/**
 * Sample of Acl
 *
 * @category    %ProjectName%
 * @package     App
 * @copyright
 */
class App_Acl_Db extends Zend_Acl
{
    /**
     * Singleton instance
     *
     * @var Acl
     */
    private static $_instance = null;

    /**
     * Set permission
     *
     */
    private function __construct()
    {
        $this->addRole(new Zend_Acl_Role(WACOW_LEVEL_EVERYONE));

        Wacow_Application::getInstance()->getDbAdapter();

        /** Add roles */
        $roleTable = System_Roles::getInstance();
        $select = $roleTable->select()
                            ->where('status = ?', 'y');
        $roleIdList = $roleTable->fetchCol('id', $select);

        foreach ($roleIdList as $roleId) {
            $this->addRole(new Zend_Acl_Role($roleId));
        }

        /** Add resources */
        $menuRowset = System_Resources::fetchRowsetGroupedByController();
        foreach ($menuRowset as $menuRow) {
        	$this->add(new Zend_Acl_Resource($menuRow->module . ':' . $menuRow->controller));
        }
        $this->add(new Zend_Acl_Resource('admin:user'));

        /** Set permission */
        $permissionTable = new System_Permissions();
        $select = $permissionTable->select()
                                  ->setIntegrityCheck(false)
                                  ->from($permissionTable)
                                  ->join('system_resources', 'system_permissions.resourceId = system_resources.id', array('resource' => "CONCAT(module, ':', controller)", 'action'));
        $permissionRowset = $permissionTable->fetchAll($select);
        foreach ($permissionRowset as $permissionRow) {
            if ('y' === $permissionRow->status) {
                $this->allow($permissionRow->roleId, $permissionRow->resource, $permissionRow->action);
            } else {
                $this->deny($permissionRow->roleId, $permissionRow->resource, $permissionRow->action);
            }
        }

        /** Special permission */
        $this->allow(WACOW_LEVEL_EVERYONE, 'default:index');
        $this->allow(WACOW_LEVEL_EVERYONE, 'admin:user', 'login');
        $this->allow(WACOW_LEVEL_EVERYONE, 'admin:user', 'logout');
    }

    /**
     * Singleton
     *
     * @return Acl
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}