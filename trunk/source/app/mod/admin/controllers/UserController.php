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
class Admin_UserController extends App_Controller_Action
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
        $this->disableLayout();

        $auth = Wacow_Controller_Plugin_Auth::getAuth();
        if ($auth->hasIdentity()) {
            $this->_redirect('/admin');
        }

        $error = false;
        $messenger = $this->_pageMessenger;
        $request = $this->getRequest();
        /* @var $messenger Wacow_Controller_Action_Helper_PageMessenger */
        /* @var $request Zend_Controller_Request_Http */

        if ($this->isPost()) {
            $filter = new Zend_Filter_StripTags();
            $identity = $filter->filter(trim($request->getPost('identity')));
            $password = $filter->filter(trim($request->getPost('password')));

            if ('' == $identity) {
                $error = true;
                $messenger->addMessage('Enter identity please.', 'identity');
            }

            if ('' == $password) {
                $error = true;
                $messenger->addMessage('Enter password please.', 'password');
            }

            $userTable = System_Users::getInstance();
            /* @var $userTable System_Users */
            if (!$error && $userTable->authenticate($identity, $password, $auth)) {
                $this->_redirect('/admin');
            } else {
                $error = true;
                $messenger->addMessage('Invalid identity or password.');
            }

            $this->view->error    = $error;
            $this->view->messages = $messenger->getMessages();
        }
    }

    /**
     * Logout
     *
     */
    public function logoutAction()
    {
        $auth = Wacow_Controller_Plugin_Auth::getAuth();
        $auth->clearIdentity();
        $this->_redirect('/admin');
    }
}