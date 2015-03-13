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
 * @version    $Id: Searcher.php 502 2008-05-25 16:20:14Z jaceju $
 */

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Build captcha image
 *
 * @category   Wacow
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Action_Helper
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Controller_Action_Helper_CaptchaBuilder extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * $_session - Zend_Session storage object
     *
     * @var Zend_Session
     */
    protected $_session = null;

    /**
     * $_namespace - Instance namespace, default is 'default'
     *
     * @var string
     */
    protected $_namespace = 'default';

    /**
     * postDispatch() - runs after action is dispatched
     *
     * @return Wacow_Controller_Action_Helper_CaptchaBuilder Provides a fluent interface
     */
    public function postDispatch()
    {
        $this->resetSession();
        return $this;
    }

    /**
     * setNamespace() - change the namespace messages are added to, useful for
     * per action controller messaging between requests
     *
     * @param  string $namespace
     * @return Wacow_Controller_Action_Helper_CaptchaBuilder Provides a fluent interface
     */
    public function setSession($namespace = 'default')
    {
        if (!$this->_session instanceof Zend_Session_Namespace) {
            $this->_session = new Zend_Session_Namespace($namespace . '_' . $this->getName());
        }
        return $this;
    }

    /**
     * resetNamespace() - reset the namespace to the default
     *
     * @return Wacow_Controller_Action_Helper_CaptchaBuilder Provides a fluent interface
     */
    public function resetSession()
    {
        $this->setSession();
        return $this;
    }

    /**
     * Validate the captcha code
     *
     * @param string $code
     * @param string $namespace
     * @return bool
     */
    public function captchaCodeValid($code, $namespace = 'default')
    {
        $code = md5(strtoupper($code));
        $this->setSession($namespace);
        return ($this->_session->captchaCheck === $code);
    }

    /**
     * Get encoded captcha code
     *
     * @return string
     */
    public function getCaptchaCode($namespace = 'default')
    {
        $this->setSession($namespace);
        return $this->_session->captchaCheck;
    }

    /**
     * Generate image to browser
     *
     * Options:
     * - backgroundImage (null)
     * - backgroundColor ('000000')
     * - width (100)
     * - height (25)
     * - fontSize (24)
     * - fontColor ('FFFFFF')
     * - fontFile ('')
     * - length (4)
     *
     * @param array $options
     */
    public function generateImage($options = array())
    {
        $backgroundImage = null;
        $backgroundColor = '000000';
        $width = 100;
        $height = 25;
        $fontSize = 24;
        $fontColor = 'FFFFFF';
        $fontFile = '';
        $length = 4;

        // process options
        if (array_key_exists('namespace', $options)) {
            $this->setSession($options['namespace']);
        } else {
            $this->setSession();
        }

        if (array_key_exists('backgroundImage', $options) && file_exists($options['backgroundImage'])) {
            $backgroundImage = $options['backgroundImage'];
        }

        if (array_key_exists('backgroundColor', $options) && preg_match('/^[0-9A-F]{6}$/i', $options['backgroundColor'])) {
            $backgroundColor = $options['backgroundColor'];
        }

        if (array_key_exists('width', $options) && is_int($options['width'])) {
            $width = $options['width'];
        }

        if (array_key_exists('height', $options) && is_int($options['height'])) {
            $height = $options['height'];
        }

        if (array_key_exists('fontSize', $options) && is_int($options['fontSize'])) {
            $fontSize = $options['fontSize'];
        }

        if (array_key_exists('fontColor', $options) && preg_match('/^[0-9A-F]{6}$/i', $options['fontColor'])) {
            $fontColor = $options['fontColor'];
        }

        if (array_key_exists('fontFile', $options) && file_exists($options['fontFile'])) {
            $fontFile = $options['fontFile'];
        } else {
            throw new Wacow_Controller_Action_Exception("Need to specify the 'fontFile' in options.");
        }

        // set image resource
        $im = imagecreate($width, $height);
        if ($backgroundImage) {
            $src = imagecreatefromjpeg($backgroundImage);
            imagecopy($im, $src, 0, 0, 0, 0, $width, $height);
        } else {
            $rgb = hexdec($backgroundColor);
            $backgroundColor = imagecolorallocate($im, 0xFF & ($rgb >> 0x10), 0xFF & ($rgb >> 0x8), 0xFF & $rgb);
            imagefill($im, 0, 0, $backgroundColor);
        }

        // build text
        $text = strtoupper(substr(md5(time() . microtime() . rand()), 0, $length));

        // register session
        $this->_session->captchaCheck = md5($text);

        $rgb = hexdec($fontColor);
        $fontColor = imagecolorallocate($im, 0xFF & ($rgb >> 0x10), 0xFF & ($rgb >> 0x8), 0xFF & $rgb);

        // info of text
        $angle   = 0;
        $textLen = mb_strlen($text, 'UTF-8');

        // get font size
        $info = imagettfbbox($fontSize, $angle, $fontFile, $text);
        $boxW = abs($info[2] - $info[0]);
        $boxH = abs($info[3] - $info[5]);

        // fix font position
        $startX = ($info[6] < 0) ? abs($info[6]) : ($info[6] > 0) ? 0 - $info[6] : 0;
        $startY = ($info[7] < 0) ? abs($info[7]) : ($info[7] > 0) ? 0 - $info[7] : 0;

        // set center
        $x = ceil(($width  - $boxW) / 2);
        $y = ceil(($height - $boxH) / 2);

        // draw text
        imagettftext($im, $fontSize, $angle, $startX + $x, $startY + $y, $fontColor, $fontFile, $text);

        // show image
        $this->getResponse()->setHeader('Content-type', 'image/png', true);
        imagepng($im);
        imagedestroy($im);
    }
}