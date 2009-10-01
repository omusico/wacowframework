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
class Admin_FileController extends App_Controller_Action
{
    /**
     * Set controller
     *
     */
    public function preDispatch()
    {
        $this->setLayout('layout');
    }

    /**
     * Default Action
     *
     */
    public function uploadAction()
    {
        $uploader = $this->uploader; /* @var $uploader Wacow_Controller_Action_Helper_Uploader */
        $uploader->bind('fileInput')->addAcceptExtension('jpg');
        for ($i = 0; $i < count($uploader); $i ++) {
            if ($uploader->get($i)->isAcceptFile()) {
                $uploader->saveAs(WF_PUB_PATH . '/files/');
            }
        }
        $uploader->bind('fileInput3')->addAcceptExtension('sql');
        if ($uploader->isAcceptFile()) {
            $uploader->saveAs(WF_PUB_PATH . '/files/');
        }
        $uploader->bind('fileInput4')->addAcceptExtension('sql');
        if ($uploader->isAcceptFile()) {
            $uploader->saveAs(WF_PUB_PATH . '/files/');
        }
    }
}