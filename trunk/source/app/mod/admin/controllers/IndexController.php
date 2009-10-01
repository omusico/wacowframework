<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @package     Admin_Controller
 * @copyright
 * @version     $Id$
 */

/**
 * @see App_Controller_Action
 */
require_once 'App/Controller/Action.php';

/**
 * Admin Controller
 *
 * @category    %ProjectName%
 * @package     Admin_Controller
 * @copyright
 */
class Admin_IndexController extends App_Controller_Action
{
    /**
     * Set controller
     *
     */
    public function preDispatch()
    {
        $this->setLayout('layout');
    }

    /**
     * Default Action
     *
     */
    public function indexAction()
    {

    }

    /**
     * Permission Action
     *
     */
    public function permissionAction()
    {
        // Get role rowset
        $this->view->roleRowset = System_Roles::fetchActivedRowset();

        // set resource list
        if ($this->isAjax()) {
            $this->view->ajax = true;
        }

        // update permission
        if ($this->isPost() && 1 === (int) $this->getRequest()->getPost('update')) {
            // drop all permission of role

            // insert new permission of role
        }

        $this->_setResourceList();
    }

    /**
     * Update permission
     *
     */
    protected function _updatePermission()
    {

    }

    /**
     * set resource list
     *
     */
    protected function _setResourceList()
    {
        // get roleId
        $this->view->roleId = $roleId = $this->getRequest()->getParam('roleId', 1);

        // get permission
        $this->view->resourceList = $resourceList = System_Permissions::getResourceListByRoleId($roleId);
    }

    /**
     * 使用者管理
     *
     */
    public function userAction()
    {

    }
}