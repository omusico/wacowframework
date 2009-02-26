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
 * @package    Wacow_Application
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Wacow_Application_PathTranslator
 *
 * @category   Wacow
 * @package    Wacow_Application
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Application_PathTranslator
{
    /**
     * Paths of runtime
     *
     * @var array
     */
    protected $_runtimePaths = array();

    /**
     * Constructor
     *
     * @param array $paths
     */
    public function __construct($paths)
    {
        $this->_runtimePaths = $paths;
    }

    /**
     * Add path mapping
     *
     * @param mixed $paths
     */
    public function addPathMapping($paths)
    {
        if (2 == func_num_args()) {
            $this->_runtimePaths[func_get_arg(0)] = func_get_arg(1);
        } elseif (is_array($paths)) {
            $this->_runtimePaths = array_merge($this->_runtimePaths, $paths);
        }
    }

    /**
     * Get runtime path
     *
     * @param string $name
     * @return string
     */
    public function getRuntimePath($name)
    {
        return $this->_runtimePaths[$name];
    }

    /**
     * Inject values into the options
     *
     * Allowed variables are:
     * - :rootPath
     * - :appPath
     * - :etcPath
     * - :tmpPath
     * - :modulePath
     * - :basePath
     * - :libPath
     * - :pubPath
     * - :cachePath
     * - :uploadPath
     * - :etcCachePath
     * - :commonPath
     *
     * @see /app/etc/defination.php
     * @param array|Zend_Config $options
     * @return Zend_Config
     */
    public function translatePath($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        $isString = is_string($options);

        $options = (array) $options;

        foreach ($options as $key => $option) {
            $options[$key] = str_replace(array_keys($this->_runtimePaths), array_values($this->_runtimePaths), $option);
        }

        return ($isString) ? $options[0] : new Zend_Config($options);
    }
}