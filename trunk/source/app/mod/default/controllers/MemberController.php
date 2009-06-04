<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @package     Default_Controller
 * @copyright
 * @version     $Id$
 */

/**
 * @see App_Controller_Action
 */
require_once 'App/Controller/Action.php';

/**
 * Member Controller
 *
 * @category    %ProjectName%
 * @package     Default_Controller
 * @copyright
 */
class MemberController extends App_Controller_Action
{
    /**
     * Index
     *
     */
    public function indexAction()
    {
        $this->_redirectAction('login');
    }

    /**
     * Login
     *
     */
    public function loginAction()
    {
    }

    /**
     * Register
     *
     */
    public function registerAction()
    {
    }

    /**
     * Logout
     *
     */
    public function logoutAction()
    {
    }
}