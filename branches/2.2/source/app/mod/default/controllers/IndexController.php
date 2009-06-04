<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @package     Default_Controller
 * @copyright
 * @version     $Id: IndexController.php 613 2009-01-06 12:18:23Z jaceju $
 */

/**
 * @see App_Controller_Action
 */
require_once 'App/Controller/Action.php';

/**
 * Default Controller
 *
 * @category    %ProjectName%
 * @package     Default_Controller
 * @copyright
 */
class IndexController extends App_Controller_Action
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

    /**
     * Test Action
     *
     */
    public function cacheAction()
    {
        $this->setLayout('layout');
        $this->setPageCaching(true);
        $this->view->pageTitle = 'Test!!';

        if (!$this->isPageCached()) {
            $time = time();
            for ($i = 0; $i < 10000000; $i ++) {
                $time += $i;
            }
            $this->view->time = $time;
        }
    }

    /**
     * Database Test
     *
     */
    public function databaseAction()
    {
        $this->noRender();
        $this->getDbConnection();
        $userTable = new System_Users();
        $row = $userTable->createRow();
        echo get_class($row);
    }
}