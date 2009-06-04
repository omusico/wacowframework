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
 * @package    Wacow_File
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Wacow_File
 *
 * @category   Wacow
 * @package    Wacow_File
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_File
{
    /**
     * Name of file.
     *
     * @var string
     */
    protected $_name = '';

    /**
     * The path of file.
     *
     * @var string
     */
    protected $_realPath = '';

    /**
     * Content of file
     *
     * @var string
     */
    protected $_content = '';

    /**
     * File constructor
     *
     * @param string $path
     */
    public function __construct($path = null)
    {
        if ($path) {
            $this->setPath($path);
        }
    }

    /**
     * Set real path of file
     *
     * @param string $path
     * @return Wacow_File
     */
    public function setPath($path)
    {
        if (!file_exists($path)) {
            throw new Wacow_File_Exception("File \"$path\" was not found");
        }
        $this->_realPath = $path;
        $this->_name     = basename($path);
        return $this;
    }

    /**
     * Get real path of file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_realPath;
    }

    /**
     * Set content for download
     *
     * @param string $content
     * @return Wacow_File
     */
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * Get content of file
     *
     * @return string
     */
    public function getContent()
    {
        if (empty($this->_content) && is_readable($this->_realPath)) {
            $this->_content = file_get_contents($this->_realPath);
        }
        return $this->_content;
    }

    /**
     * Get size of file
     *
     * @return int
     */
    public function getSize()
    {
        if ($this->_content) {
            return strlen($this->_content);
        } elseif (file_exists($this->_realPath)) {
            return filesize($this->_realPath);
        }
    }

    /**
     * Set name of file. It will replace the name from real path.
     *
     * @param string $name
     * @return Wacow_File
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Get name of file.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Output file
     *
     * @return void
     */
    public function read()
    {
        if ($this->_content) {
            echo $this->_content;
        } elseif (is_readable($this->_realPath)) {
            readfile($this->_realPath);
        } else {
            throw new Wacow_File_Exception("Can not read content of file.");
        }
    }

    /**
     * Delete file
     *
     * @return void
     */
    public function delete()
    {
        if (file_exists($this->_realPath)) {
            unlink($this->_realPath);
        } else {
            throw new Wacow_File_Exception("Can not delete file.");
        }
    }

    /**
     * Save file
     *
     * @param string $path
     */
    public function save($path = null)
    {
        if ($path) {
            $this->_realPath = $path;
        }

        file_put_contents($this->_realPath, $this->_content);
    }
}