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
 * @version    $Id: ImageResizer.php 502 2008-05-25 16:20:14Z jaceju $
 */

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Image Resizer
 *
 * @category   Wacow
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Action_Helper
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Controller_Action_Helper_ImageResizer extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Path of source image
     *
     * @var string
     */
    private $_srcPath;

    /**
     * Width of source image
     *
     * @var int
     */
    private $_srcWidth;

    /**
     * Height of source image
     *
     * @var int
     */
    private $_srcHeight;

    /**
     * Type of source image
     *
     * @var string
     */
    private $_srcType;

    /**
     * Attribute of source image
     *
     * @var string
     */
    private $_srcAttribute;

    /**
     * Ratio of w/h of source image
     *
     * @var float
     */
    private $_srcRatio = 1;

    /**
     * Path of destination image
     *
     * @var string
     */
    private $_destPath;

    /**
     * Ratio of w/h of destination image
     *
     * @var float
     */
    private $_destRatio = 1;

    /**
     * Quality of destination image (for JPEG)
     *
     * @var int
     */
    private $_destQuality = 100;

    /**
     * Information of destination image
     *
     * @var array
     */
    private $_destInfo;

    /**
     * Max width of destination image
     *
     * @var int
     */
    private $_maxWidth;

    /**
     * Max height of destination image
     *
     * @var int
     */
    private $_maxHeight;

    /**
     * Fill the blank canvas
     *
     * @var boolean
     */
    private $_fill = true;

    /**
     * The color for fill
     *
     * @var array
     */
    private $_fillColor = array(0xFF, 0xFF, 0xFF);

    /**
     * Set path of source image
     *
     * @param string $sourcePath
     */
    public function setSource($sourcePath)
    {
        if (!file_exists($sourcePath)) {
            throw new Zend_Controller_Action_Exception('Source Image is not available!');
        }

        list(
            $this->_srcWidth,
            $this->_srcHeight,
            $this->_srcType,
            $this->_srcAttribute
        ) = getimagesize($sourcePath);
        $this->_srcPath   = $sourcePath;
        $this->_srcRatio  = $this->_srcWidth / $this->_srcHeight;
    }

    /**
     * Set destination canvas
     *
     * @param string $destinationPath
     * @param int $maxWidth
     * @param int $maxHeight
     * @param boolean $fill
     * @param int $quality
     */
    public function setDestination($destinationPath, $maxWidth, $maxHeight, $fill = false, $bgColor = array(0xFF, 0xFF, 0xFF), $quality = 100)
    {
        $this->_destPath  = $destinationPath;
        $this->_destInfo  = pathinfo($destinationPath);
        $this->_maxWidth  = ($w = (int) $maxWidth)  ? $w : 1;
        $this->_maxHeight = ($h = (int) $maxHeight) ? $h : 1;
        $this->_fill = (bool) $fill;
        $this->_destRatio = $this->_maxWidth / $this->_maxHeight;
    }

    /**
     * Resize the image
     *
     */
    public function resize()
    {
        $destImage  = null;
        $destWidth  = $this->_maxHeight;
        $destHeight = $this->_maxWidth;

		$xRatio = $this->_maxWidth  / $this->_srcWidth;   // The width radio of max/source
		$yRatio = $this->_maxHeight / $this->_srcHeight;  // The height radio of max/source

        // Set size of destination image
		if (($this->_srcWidth <= $this->_maxWidth) && ($this->_srcHeight <= $this->_maxHeight)) {
			$destWidth  = $this->_srcWidth;
			$destHeight = $this->_srcHeight;
		}

		if ($xRatio < $yRatio) {
            // Base on width if w radio less then h radio
			$destHeight = ceil($xRatio *  $this->_srcHeight);
			$destWidth  = $this->_maxWidth;
		} else {
		    // Base on height if w radio great then h radio
            $destWidth  = ceil($yRatio * $this->_srcWidth);
            $destHeight = $this->_maxHeight;
        }

        $destImage = imagecreatetruecolor($destWidth, $destHeight);

        // Get the image of source with mimetype
        $srcImage    = null;
        $srcMimeType = strtolower(image_type_to_mime_type($this->_srcType));
        switch ($srcMimeType) {
            case 'image/gif':
                $srcImage = imagecreatefromgif($this->_srcPath);
                break;
            case 'image/jpeg':
                $srcImage = imagecreatefromjpeg($this->_srcPath);
                break;
            case 'image/png':
                $srcImage = imagecreatefrompng($this->_srcPath);
                 break;
            default:
                break;
        }

        // Resample the image
        if (!imagecopyresampled(
                $destImage, $srcImage,
                0, 0, 0, 0,
                $destWidth, $destHeight,
                $this->_srcWidth, $this->_srcHeight)) {
            throw new Zend_Controller_Action_Exception("Can not resample \"{$this->_srcPath}\"!");
        }
        $resultImage = &$destImage;

        // If we want to fill the canvas, the image would place in the central
        if ($this->_fill) {

            $fillImage = imagecreatetruecolor($this->_maxWidth, $this->_maxHeight);
            $params  = array_merge(array($fillImage), $this->_fillColor);
            $bgColor = call_user_func_array('imagecolorallocate', $params);
            imagefill($fillImage, 0, 0, $bgColor);

            $destX = ceil(($this->_maxWidth  / 2) - ($destWidth  / 2));
            $destY = ceil(($this->_maxHeight / 2) - ($destHeight / 2));

            imagecopymerge(
                $fillImage, $resultImage,
                $destX, $destY, 0, 0,
                $destWidth, $destHeight, 100);

            // Rebuild the image
            imagedestroy($resultImage);
            $resultImage = &$fillImage;
        }

        // Output
        switch ($srcMimeType) {
            case 'image/gif':
                imagegif($resultImage, $this->_destPath);
                break;
            case 'image/jpeg':
                imagejpeg($resultImage, $this->_destPath, $this->_destQuality);
                break;
            case 'image/png':
                imagepng($resultImage, $this->_destPath);
                break;
        }

        imagedestroy($srcImage);
        imagedestroy($resultImage);
    }
}