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
 * @version    $Id: Auth.php 693 2009-07-16 07:45:50Z jaceju $
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
    private static $_auth;

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
     * Namespace for auth.
     *
     * @var string
     */
    private $_namespace = ':moduleName';

    /**
     * Field name of roleId
     *
     * @var string
     */
    private $_roleIdFieldName = 'roleId';

    /**
     * Storage for auth.
     *
     * @var Zend_Auth_Storage_Interface
     */
    private $_storage = null;

    /**
     * Login
     *
     * @var array
     */
    private $_loginHandler = array(
        'module'     => ':moduleName',
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
        self::$_auth = Zend_Auth::getInstance();
        $this->_acl = $acl;

        if (array_key_exists('defaultRoleId', $settings)) {
            $this->_defaultRoleId = $settings['defaultRoleId'];
        }

        if (array_key_exists('namespace', $settings)) {
            $this->_namespace = $settings['namespace'];
        }

        if (array_key_exists('loginHandler', $settings)) {
            $this->_loginHandler = $settings['loginHandler'];
        }

        if (array_key_exists('denyHandler', $settings)) {
            $this->_denyHandler = $settings['denyHandler'];
        }

        if (array_key_exists('storage', $settings)
                && $settings['storage'] instanceof Zend_Auth_Storage_Interface) {
            $this->_storage = $settings['storage'];
        }

        if (array_key_exists('roleIdFieldName', $settings)
                && is_string($settings['roleIdFieldName'])) {
            $this->_roleIdFieldName = $settings['roleIdFieldName'];
        }
    }

    /**
     * Authenticate
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if (null === $this->_storage) {
            $app = Wacow_Application::getInstance();
            $namespace = strtoupper($app->name . '_' . $this->_translateName($this->_namespace));
            $this->_storage = new Zend_Auth_Storage_Session($namespace);
        }
        self::$_auth->setStorage($this->_storage);

        if (self::$_auth->hasIdentity()) {
            $userData = self::$_auth->getIdentity();
            $roleId = $userData->{$this->_roleIdFieldName};
        } else {
            $roleId = $this->_defaultRoleId;
        }

        $moduleName     = strtolower($request->getModuleName());
        $controllerName = strtolower($request->getControllerName());
        $actionName     = strtolower($request->getActionName());
        $resource       = $moduleName . ':' . $controllerName;

        if (!$this->_acl->has($resource)) {
            $resource = null;
        }

        if (!$this->_acl->isAllowed($roleId, $resource, $actionName)) {
            if (!self::$_auth->hasIdentity()) {
                $moduleName     = $this->_translateName($this->_loginHandler['module']);
                $controllerName = $this->_translateName($this->_loginHandler['controller']);
                $actionName     = $this->_translateName($this->_loginHandler['action']);
            } else {
                $moduleName     = $this->_translateName($this->_denyHandler['module']);
                $controllerName = $this->_translateName($this->_denyHandler['controller']);
                $actionName     = $this->_translateName($this->_denyHandler['action']);
            }
        }
        $request->setModuleName($moduleName);
        $request->setControllerName($controllerName);
        $request->setActionName($actionName);
    }

    /**
     * Get auth object
     *
     * @return Zend_Auth
     */
    public function getAuth()
    {
        return self::$_auth;
    }

    /**
     * Translate name
     *
     * @param string $name
     * @return string
     */
    protected function _translateName($name)
    {
        switch ($name) {
        	case ':moduleName':
        		return $this->getRequest()->getModuleName();
        		break;
        	case ':controllerName':
        		return $this->getRequest()->getControllerName();
        		break;
        	case ':actionName':
        		return $this->getRequest()->getActionName();
        		break;
        	default:
        	    return $name;
        		break;
        }
    }
}