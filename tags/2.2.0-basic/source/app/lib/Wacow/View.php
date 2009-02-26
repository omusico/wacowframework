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
 * @package    Wacow_View
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: View.php 548 2008-06-23 06:45:12Z jaceju $
 */

/**
 * @see Zend_View
 */
require_once 'Zend/View.php';

/**
 * @category   Wacow
 * @package    Wacow_View
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_View extends Zend_View
{
    /**
     * Static view options
     *
     * @var array
     */
    protected static $_staticOptions = array();

    /**
     * View options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Set view options
     *
     * @param array $options
     */
    public static function setStaticOptions(array $options)
    {
        self::$_staticOptions = $options;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getStaticOptions()
    {
        return self::$_staticOptions;
    }

    /**
     * Class constructor
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->_options = $config;
        parent::__construct($config);
    }

    /**
     * Get view options
     *
     * @param string $key
     * @return mixed
     */
    public function getOption($key = null)
    {
        if ($key && array_key_exists($key, $this->_options)) {
            return $this->_options[$key];
        } else {
            return $this->_options;
        }
    }

    /**
     * Factory method
     *
     * @param string $type
     * @param array $options
     * @return Wacow_View
     */
    public function factory($type, $options = array())
    {
        self::$_staticOptions = array_merge(self::$_staticOptions, (array) $options);
        if ('html' == $type) {
            return Wacow_View_Html::factory(self::$_staticOptions);
        } elseif ('' != trim($type)) {
            $className = 'Wacow_View_' . ucfirst(strtolower($type));
            return new $className(self::$_staticOptions);
        } else {
            return new Wacow_View(self::$_staticOptions);
        }
    }

    /**
     * Set content of layout
     *
     * @param Zend_Controller_Request $layoutContents
     */
    public function setLayoutContents($layoutContents)
    {
    }

    /**
     * Set variables for frontend.
     *
     * @param Zend_Controller_Request $request
     */
    public function setFrontendVars()
    {
    }

    /**
     * Assign frontend variables.
     *
     * @param string $name
     * @param mixed $value
     */
    public function assignFrontendVar($name, $value)
    {
    }

    /**
     * Finds a view script from the available directories.
     *
     * @param $name string The base name of the script.
     * @return void
     */
    protected function _script($name)
    {
        try {
            return parent::_script($name);
        } catch (Zend_View_Exception $e) {
            $commonTemplateDir = $this->getOption('commonTemplateDir');
            $name = str_replace(':common', $commonTemplateDir, $name);
            if (is_readable($name)) {
                return $name;
            }
            require_once 'Zend/View/Exception.php';
            $message = "script '$name' not found";
            throw new Zend_View_Exception($message, $this);
        }
    }
}

