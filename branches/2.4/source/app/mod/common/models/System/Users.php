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
class System_Users extends App_Db_Table
{
    /**
     * Table Name
     *
     * @var string
     */
    protected $_name = 'system_users';

    /**
     * Row Class
     *
     * @var string
     */
    protected $_rowClass = 'System_User';

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
    protected $_dependentTables = array('System_Permissions');

    /**
     * self instance
     *
     * @var System_Users
     */
    private static $_instance = null;

    /**
     * Row of user of current session
     *
     * @var System_User
     */
    private static $_currentUserRow;

    /**
     * Get instance
     *
     * @return System_Users
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Auth object
     *
     * @var Zend_Auth
     */
    protected $_auth = null;

    /**
     * Set auth object
     *
     * @param Zend_Auth $auth
     */
    public function setAuth(Zend_Auth $auth)
    {
        $this->_auth = $auth;
    }

    /**
     * Fetch row by identity
     *
     * @param string $identity
     * @return System_User
     */
    protected function _fetchRowByIdentity($identity)
    {
        $select = $this->select()->where('identity = ?', $identity)->limit(1);
        return $this->fetchRow($select);
    }

    /**
     * encode password
     *
     * @param string $password
     * @return string
     */
    public static function encodePassword($password)
    {
        return md5($password);
    }

    /**
     * User authenticate
     *
     * @param string $identity
     * @param string $password
     * @param Zend_Auth $auth
     */
    public function authenticate($identity, $password)
    {
        $auth = Zend_Auth::getInstance();
        if ($userRow = $this->_fetchRowByIdentity($identity)) {
            if (self::encodePassword($password) !== $userRow->password) {
                $userRow->passwordFail();
                return false;
            } else {
                self::$_currentUserRow = $userRow;
                $this->reloadSessionData($auth);
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Reload session data from database
     *
     */
    public function reloadSessionData()
    {
        if ($userRow = $this->fetchCurrentUserRow()) {
            $userData = $userRow->toArray();
            unset($userData['password']);
            Zend_Auth::getInstance()->getStorage()->write((object) $userData);
        }
    }

    /**
     * Find user data of current session
     *
     * @param array $data
     * @return System_User
     */
    public function fetchCurrentUserRow()
    {
        if (null == self::$_currentUserRow) {
            $auth = Zend_Auth::getInstance();
            if ($auth->hasIdentity()) {
                $userData = $auth->getIdentity();
                self::$_currentUserRow = $this->_fetchRowByIdentity($userData->identity);
            }
        }
        return self::$_currentUserRow;
    }
}