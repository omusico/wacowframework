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
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Plugin
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Auth.php 406 2008-04-20 02:43:18Z jaceju $
 */

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Redirect to login if nonauthenticate.
 *
 * @category   Wacow
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Plugin
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    /**
     * Auth object
     *
     * @var Zend_Auth
     */
    private $_auth;

    /**
     * Acl object
     *
     * @var Zend_Acl
     */
    private $_acl;

    /**
     * Default role id
     *
     * @var mixed
     */
    private $_defaultRoleId = null;

    /**
     * Login
     *
     * @var array
     */
    private $_loginHandler = array(
        'module'     => 'default',
        'controller' => 'user',
        'action'     => 'login'
    );

    /**
     * No privileges
     *
     * @var array
     */
    private $_denyHandler = array(
        'module'     => 'default',
        'controller' => 'error',
        'action'     => 'privilege'
    );

    /**
     * Constructor
     *
     * @param Zend_Auth $auth
     * @param Zend_Acl $acl
     * @param array $settings
     */
    public function __construct(Zend_Acl $acl, array $settings = array())
    {
        $this->_auth = Zend_Auth::getInstance();
        $this->_acl = $acl;

        if (array_key_exists('defaultRoleId', $settings)) {
            $this->_defaultRoleId = $settings['defaultRoleId'];
        }

        if (array_key_exists('loginHandler', $settings)) {
            $this->_loginHandler = $settings['loginHandler'];
        }

        if (array_key_exists('denyHandler', $settings)) {
            $this->_denyHandler = $settings['denyHandler'];
        }
    }

    /**
     * Authenticate
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if ($this->_auth->hasIdentity()) {
            $userData = $this->_auth->getIdentity();
            $roleId = $userData->roleId;
        } else {
            $roleId = $this->_defaultRoleId;
        }

        $moduleName     = $request->getModuleName();
        $controllerName = $request->getControllerName();
        $actionName     = $request->getActionName();
        $resource       = $moduleName . ':' . $controllerName;

        if (!$this->_acl->has($resource)) {
            $resource = null;
        }

        if (!$this->_acl->isAllowed($roleId, $resource, $actionName)) {
            if (!$this->_auth->hasIdentity()) {
                $moduleName     = $this->_loginHandler['module'];
                $controllerName = $this->_loginHandler['controller'];
                $actionName     = $this->_loginHandler['action'];
            } else {
                $moduleName     = $this->_denyHandler['module'];
                $controllerName = $this->_denyHandler['controller'];
                $actionName     = $this->_denyHandler['action'];
            }
        }
        $request->setModuleName($moduleName);
        $request->setControllerName($controllerName);
        $request->setActionName($actionName);
    }
}