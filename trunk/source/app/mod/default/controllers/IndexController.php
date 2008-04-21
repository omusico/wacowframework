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
 * Default Controller
 *
 * @category    ProjectName
 * @package     Default_Controller
 * @copyright
 */
class IndexController extends Controller_Action
{
    /**
     * Default Action
     *
     */
    public function indexAction()
    {
        $this->setLayout('layout');
        $this->view->pageTitle = 'Congratulations!!';
    }
}