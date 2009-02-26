<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @package     Admin_Controller
 * @copyright
 * @version     $Id$
 */

/**
 * @see App_Controller_Action
 */
require_once 'App/Controller/Action.php';

/**
 * Admin Controller
 *
 * @category    %ProjectName%
 * @package     Admin_Controller
 * @copyright
 */
class Admin_IndexController extends App_Controller_Action
{
    /**
     * Default Action
     *
     */
    public function indexAction()
    {
        $this->setLayout('layout');
    }
}