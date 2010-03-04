<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @package     App_Controller
 * @copyright
 * @version     $Id$
 */

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Action Helper Loader
 *
 * @category   %ProjectName%
 * @package    App_Controller
 * @copyright
 */
class App_Controller_Action_Helper_AdminMenuHandler extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Set resource
     *
     */
    public function preDispatch()
    {
        // Set request
        $request = $this->getRequest();
        /* @var $request Zend_Controller_Request_Http */
        if ($request->isXmlHttpRequest()) {
            return null;
        }

        // Set auth and acl
        $auth = Wacow_Controller_Plugin_Auth::getAuth();
        if (!$auth->hasIdentity()) {
            return null;
        }
        $userData = $auth->getIdentity();
        $acl = Wacow_Application::getInstance()->getAcl();

        // Get resource
        $resourceRowset = System_Resources::fetchActivedRowset('y', 'admin');

        // Build menu
        $menuList = array();
        foreach ($resourceRowset as $resourceRow) {
            $resource = $resourceRow->module . ':' . $resourceRow->controller;
            $privilege = $resourceRow->action;
            if ($acl->isAllowed($userData->roleId, $resource, $privilege)) {
                $url = '/' . $resourceRow->module . '/' . $resourceRow->controller . '/' . $resourceRow->action;
                if ($resourceRow->parentId) {
                    $menuList[$resourceRow->parentId]['subMenuList'][$resourceRow->id] = array(
                        'name' => $resourceRow->name,
                        'url'  => $url,
                    );
                } else {
                    $menuList[$resourceRow->id] = array(
                        'name' => $resourceRow->name,
                        'url'  => $url,
                        'subMenuList' => array(),
                    );
                }
            }
        }
        $this->_actionController->view->menuList = $menuList;
    }
}