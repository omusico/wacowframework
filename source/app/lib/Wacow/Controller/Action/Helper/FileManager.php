<?php
/**
 * Wacow Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Wacow
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Action_Helper
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: FileManager.php 502 2008-05-25 16:20:14Z jaceju $
 */

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * File Manager
 *
 * @category   Wacow
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Action_Helper
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Controller_Action_Helper_FileManager extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Content of file
     *
     * @var string
     */
    protected $_content = '';

    /**
     * Create a file object.
     *
     * @param string $name
     * @return Wacow_File
     */
    public function createFile($name = null)
    {
        $file = new Wacow_File();
        if ($name) {
            $file->setName($name);
        }
        return $file;
    }

    /**
     * Set path of file
     *
     * @param string $path
     * @param bool $deleteAfterDownload
     * @return Wacow_File
     */
    public function loadFile($path)
    {
        if (file_exists($path)) {
            return new Wacow_File($path);
        } else {
            throw new Zend_Controller_Action_Exception("'$path' is not a file!");
        }
    }

    /**
     * Output content for browser downloading.
     *
     * @param string $fileName
     */
    public function setDownloadHeader(Wacow_File $file)
    {
        $fileSize = $file->getSize();
        $fileName = $file->getName();

        header('Pragma: public');
        header('Expires: 0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: attachment; filename="' . $fileName . '";');
        header('Content-Transfer-Encoding: binary');
    }
}