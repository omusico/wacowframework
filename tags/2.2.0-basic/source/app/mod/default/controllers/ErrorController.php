<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @package     Default_Controller
 * @copyright
 * @version     $Id: ErrorController.php 637 2009-02-25 03:19:49Z jaceju $
 */

/**
 * @see App_Controller_Action
 */
require_once 'App/Controller/Action.php';

/**
 * Error Controller
 *
 * @category    %ProjectName%
 * @package     Default_Controller
 * @copyright
 */
class ErrorController extends App_Controller_Action
{
    /**
     * Error
     *
     */
    public function errorAction()
    {
        $this->setLayout('layout');
        $this->view->pageTitle = 'Oops!';
    }

    /**
     * Privilege
     *
     */
    public function privilegeAction()
    {
    }
}