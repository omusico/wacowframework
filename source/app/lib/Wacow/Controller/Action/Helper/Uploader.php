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
 * @version    $Id$
 */

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Wacow/File/MimeType.php';

/**
 * Uploader
 *
 * @category   Wacow
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Action_Helper
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Controller_Action_Helper_Uploader extends Zend_Controller_Action_Helper_Abstract implements Countable
{
    /**
     * field name
     *
     * @var string
     */
    protected $_fieldName = '';

    /**
     * Data from $_FILES
     *
     * @var array
     */
    protected $_originalFile = null;

    /**
     * Multiple file index
     *
     * @var int
     */
    protected $_index = 0;

    /**
     * Multiple file count
     *
     * @var int
     */
    protected $_count = 0;

    /**
     * Infomation of uploaded files.
     *
     * @var array
     */
    protected $_fileInfo = array();

    /**
     * The accept extensions.
     *
     * @var array
     */
    protected $_acceptMimeTypes = array();

    /**
     * Max file size
     *
     * @var int
     */
    protected $_maxSize = 0;

    /**
     * @var boolean
     */
    protected $_hasUploadFile = false;

    /**
     * @var boolean
     */
    protected $_isUploadSuccess = false;

    /**
     * @var boolean
     */
    protected $_isAcceptExtension = false;

    /**
     * @var boolean
     */
    protected $_isAcceptSize = false;

    /**
     * Save path of file
     *
     * @var string
     */
    protected $_fileSavePath = '';

    /**
     * Reset all attributes
     *
     * @return Wacow_Controller_Action_Helper_Uploader
     */
    public function reset()
    {
        $this->_originalFile = null;
        $this->_index = 0;
        $this->_count = 0;
        $this->_fileInfo = array();
        $this->_acceptMimeTypes = array();
        $this->_maxSize = 0;
        $this->_hasUploadFile = false;
        $this->_isUploadSuccess = false;
        $this->_isAcceptExtension = false;
        $this->_isAcceptSize = false;
        return $this;
    }

    /**
     * Bind file
     *
     * @param string $fieldName
     * @return Wacow_Controller_Action_Helper_Uploader
     */
    public function bind($fieldName)
    {
        $this->reset();
        $this->_fieldName = $fieldName;
        $this->_setCount();
        return $this;
    }

    /**
     * Set count
     *
     */
    protected function _setCount()
    {
        $this->_count = 0;
        if (isset($_FILES[$this->_fieldName])) {
            if (is_array($_FILES[$this->_fieldName])) {
                $this->_count = count($_FILES[$this->_fieldName]['name']);
            } else {
                $this->_count = 1;
            }
        }
    }

    /**
     * Get count
     *
     * @return int
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Get file info
     *
     * @param int $index
     * @return Wacow_Controller_Action_Helper_Uploader
     */
    public function get($index = 0)
    {
        $this->_originalFile = $this->_convertUploadFile($index);
        $this->_hasUploadFile = isset($this->_originalFile['error']) && (UPLOAD_ERR_NO_FILE !== $this->_originalFile['error']);
        $this->_isUploadSuccess = isset($this->_originalFile['error']) && (UPLOAD_ERR_OK === $this->_originalFile['error']);
        $this->_maxSize = Wacow_Util::convertBytes(ini_get('upload_max_filesize'));

        if ($this->_isUploadSuccess) {
            $this->_fileInfo = pathinfo($this->_originalFile['name']);
        }
        return $this;
    }

    /**
     * 轉換成陣列型態
     *
     * @param string $fieldname
     * @param int $index
     * @return array
     */
    protected function _convertUploadFile($index)
    {
        $file = isset($_FILES[$this->_fieldName])
                ? $_FILES[$this->_fieldName]
                : array(
                    'name' => '',
                    'type' => '',
                    'tmp_name' => '',
                    'error' => UPLOAD_ERR_NO_FILE,
                    'size' => 0,
                );
        if (is_array($file['name'])) {
            $file = array(
                'name'      => $file['name'][$index],
                'type'      => $file['type'][$index],
                'tmp_name'  => $file['tmp_name'][$index],
                'error'     => $file['error'][$index],
                'size'      => $file['size'][$index],
            );
        }
        return $file;
    }

    /**
     * Add accept extension
     *
     * @param string|array $extension
     * @return Wacow_Controller_Action_Helper_Uploader
     */
    public function addAcceptExtension($extension)
    {
        $extension = array_map(array(__CLASS__, '_trimDot'), (array) $extension);

        $this->_acceptMimeTypes = array_merge(
            $this->_acceptMimeTypes, $this->_getTypeFromExtension($extension)
        );
        return $this;
    }

    /**
     * Get the mimetype from extensions
     *
     * @param mixed $extension
     */
    protected function _getTypeFromExtension($extensionList)
    {
        $extensionList = (array) $extensionList;
        $mimetypeList = array_map(array('Wacow_File_MimeType', 'getTypeFromExtension'), $extensionList);
        foreach ($mimetypeList as $key => $mimetype) {
        	if (is_array($mimetype)) {
        	    $mimetypeList = array_merge($mimetypeList, $mimetype);
            	unset($mimetypeList[$key]);
        	}
        }
        return array_unique($mimetypeList);
    }

    /**
     * Set the max size
     *
     * @param int $maxSize
     * @return Wacow_Controller_Action_Helper_Uploader
     */
    public function setMaxSize($maxSize)
    {
        if (is_string($maxSize)) {
            $maxSize = Wacow_Util::convertBytes($maxSize);
        }
        $maxSize = (int) $maxSize;
        if (1 < $maxSize && $maxSize < $this->_maxSize) {
            $this->_maxSize = $maxSize;
        }
        return $this;
    }

    /**
     * Trim the dot of extension.
     *
     * @param string $extension
     * @return string
     */
    protected function _trimDot($extension)
    {
        return ltrim($extension, '.');
    }

    /**
     * Get uploaded file extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->_hasUploadFile
             ? $this->_fileInfo['extension']
             : '';
    }

    /**
     * Return true if upload file exists.
     *
     * @return bool
     */
    public function hasUploadFile()
    {
        return $this->_hasUploadFile;
    }

    /**
     * If accept file
     *
     * @return bool
     */
    public function isAcceptFile()
    {
        if (null === $this->_originalFile) {
            $this->get(0);
        }

        if ($this->_hasUploadFile) {
            $this->_isAcceptExtension = count($this->_acceptMimeTypes) && isset($this->_originalFile['type'])
                                      ? in_array($this->_originalFile['type'], $this->_acceptMimeTypes)
                                      : true;
            $this->_isAcceptSize = ($this->_originalFile['size'] < $this->_maxSize)
                                && (UPLOAD_ERR_FORM_SIZE !== $this->_originalFile['error']);
            return $this->_isAcceptExtension && $this->_isAcceptSize;
        }
        return false;
    }

    /**
     * Return true when extension is not accepted.
     *
     * @return bool
     */
    public function notAcceptExtension()
    {
        return !$this->_isAcceptExtension;
    }

    /**
     * Return true when size is not accepted.
     *
     * @return bool
     */
    public function notAcceptSize()
    {
        return !$this->_isAcceptSize;
    }

    /**
     * Save uploaded file
     *
     * @param string $savePath
     * @param string $newFileName
     * @param string $newFileExt
     * @return bool
     */
    public function saveAs($savePath, $newFileName = null, $newFileExt = null)
    {
        if (null === $this->_originalFile) {
            $this->get(0);
        }

        $savePath = rtrim($savePath, '/') . '/';

        if (!$newFileName && isset($this->_fileInfo['filename'])) {
            $newFileName = $this->_fileInfo['filename'];
        }

        if (!$newFileExt && isset($this->_fileInfo['extension'])) {
            $newFileExt = $this->_fileInfo['extension'];
        }
        $newFileExt = '.' . ltrim($newFileExt, '.');

        $this->_fileSavePath = $savePath . $newFileName . $newFileExt;

        return @move_uploaded_file($this->_originalFile['tmp_name'], $this->_fileSavePath);
    }

    /**
     * Get save path of file
     *
     * @return string
     */
    public function getFileSavePath()
    {
        return $this->_fileSavePath;
    }
}