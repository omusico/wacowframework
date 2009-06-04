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
 * User Controller
 *
 * @category    %ProjectName%
 * @package     Default_Controller
 * @copyright
 */
class UserController extends App_Controller_Action
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
     * Logout
     *
     */
    public function logoutAction()
    {
    }
}