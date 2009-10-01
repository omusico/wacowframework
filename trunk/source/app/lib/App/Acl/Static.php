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
class App_Acl_Static extends Zend_Acl
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

//        /** Add resources */
//        foreach (self::$_moduleList as $moduleName => $controllerList) {
//            foreach (array_keys($controllerList) as $controllerName) {
//                $this->add(new Zend_Acl_Resource($moduleName . ':' . $controllerName));
//                if ('default' == $moduleName) {
//                    $this->allow(WACOW_LEVEL_EVERYONE, $moduleName . ':' . $controllerName);
//                }
//            }
//        }
//        /** Add roles */
//        $this->addRole(new Zend_Acl_Role(WACOW_LEVEL_MEMBER));
//        $this->addRole(new Zend_Acl_Role(WACOW_LEVEL_ADMIN));
//
//        /** Common permissions */
//        $this->allow(WACOW_LEVEL_MEMBER,   'default:member');
//
//        /** Special permissions */
//        $this->allow(WACOW_LEVEL_EVERYONE, 'default:member', 'login');
//        $this->allow(WACOW_LEVEL_EVERYONE, 'default:member', 'register');
//
//        /** Admin permission */
//        $this->allow(WACOW_LEVEL_ADMIN);
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