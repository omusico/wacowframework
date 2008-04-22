<?php
/**
 * %Project Name%
 *
 * %License or Copyright Description%
 *
 * @category    ProjectName
 * @package     Default_Controller
 * @copyright
 * @version     $Id$
 */

/**
 * @see Controller_Action
 */
require_once 'Controller/Action.php';

/**
 * Error Controller
 *
 * @category    ProjectName
 * @package     Default_Controller
 * @copyright
 */
class ErrorController extends Controller_Action
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
}