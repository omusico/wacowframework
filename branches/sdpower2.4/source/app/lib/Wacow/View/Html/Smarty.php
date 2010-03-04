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
 * @version    $Id: Smarty.php 577 2008-10-09 20:51:02Z jaceju $
 */

/**
 * @see Wacow_View_Html
 */
require_once 'Wacow/View/Html.php';

/**
 * @see Smarty
 */
require_once 'Wacow/vendor/Smarty/Smarty.class.php';

/**
 * @category   Wacow
 * @package    Wacow_View
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://akrabat.com/2006/12/23/extending-zend_view_interface-for-use-with-smarty/
 * @see        http://naneau.nl/2007/05/10/smarty-and-the-zend-framework/
 */
class Wacow_View_Html_Smarty extends Wacow_View_Html
{
    /**
     * Smarty object
     * @var Smarty
     */
    protected $_smarty;

    /**
     * Current template dir
     *
     * @var string
     */
    protected $_viewDir = null;

    /**
     * Loaded cascading style sheets
     *
     * @var array
     */
    public $loadedStyleSheets = array();

    /**
     * Loaded javascripts
     *
     * @var array
     */
    public $loadedScripts = array();

    /**
     * Consturctor
     *
     * Add path to smarty plugins
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        // smarty object
        $this->_smarty = new Smarty();

        // template dir
        if (isset($options['viewDir'])) {
            $this->_viewDir = trim($options['viewDir']);
        }

        // compile dir must be set
        if (!isset($options['compileDir'])) {
            throw new Exception('compileDir must be set in $options for ' . get_class($this));
        } else {
            $this->_smarty->compile_dir = $options['compileDir'];
        }

        // configuration files directory
        if (isset($options['configDir'])) {
            $this->_smarty->config_dir = $options['configDir'];
        }

        // custom caches directory
        if (isset($options['cacheDir'])) {
            $this->_smarty->cache_dir = $options['cacheDir'];
        }

        // delimiter
        if (isset($options['leftDelimiter'])) {
            $this->_smarty->left_delimiter = $options['leftDelimiter'];
        }

        // delimiter
        if (isset($options['rightDelimiter'])) {
            $this->_smarty->right_delimiter = $options['rightDelimiter'];
        }

        // custom plugins directory
        $pluginsDir = dirname(__FILE__) . '/Smarty/plugins';
        $this->_smarty->plugins_dir[] = $pluginsDir;

        // load custom filters
        if (isset($options['filters']) && is_array($options['filters'])) {
            foreach ($options['filters'] as $name => $type)
            $this->_smarty->load_filter(strtolower($type), strtolower($name));
        }

        // call parent constructor
        parent::__construct($options);
    }

    /**
     * Return the template engine object
     *
     * @return Smarty
     */
    public function getEngine()
    {
        return $this->_smarty;
    }

    /**
     * Return true if cached
     *
     * @param string $name
     * @return boolean
     */
    public function isCached($name, $cacheId)
    {
        $path = $this->getScriptPaths();
        $file = $path[0] . $name . '.' . $this->_options['viewSuffix'];
        return (bool) $this->_smarty->is_cached($file, $cacheId);
    }

    /**
     * Set smarty cache flag
     *
     * @param boolean|int $caching
     * @return Wacow_View_Html
     */
    public function setCaching($caching)
    {
        $this->_smarty->caching = $caching;
        return $this;
    }

    /**
     * Set smarty cache time
     *
     * @param boolean|int $caching
     * @return Wacow_View_Html
     */
    public function setCacheLifeTime($cacheLifeTime)
    {
        $this->_smarty->cache_lifetime = $cacheLifeTime;
        return $this;
    }

    /**
     * Processes a view script and returns the output.
     *
     * @param string $name The script script name to process.
     * @return string The script output.
     */
    public function render($name)
    {
        if (null !== $this->_viewDir) {
            $this->addBasePath($this->_viewDir);
        }
        return parent::render($name);
    }

    /**
     * Fetch result of render template
     *
     * @param string $name
     * @return string
     */
    public function fetch($name)
    {
        return $this->render($name . '.' . $this->_options['viewSuffix']);
    }

    /**
     * Fetch a template, echos the result,
     *
     * @see Zend_View_Abstract::render()
     * @param string $name
     * @return void
     */
    protected function _run()
    {
        $this->strictVars(true);

        // assign variables to the template engine
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ('_' != substr($key, 0, 1)) {
                $this->_smarty->assign($key, $value);
            }
        }

        // why 'this'?
        // to emulate standard zend view functionality
        // doesn't mess up smarty in any way
        $this->_smarty->assign_by_ref('this', $this);

        // smarty needs a template_dir, and can only use templates,
        // found in that directory, so we have to strip it from the filename
        $path = $this->getScriptPaths();

        $file = func_get_arg(0);
        if (!is_readable($file)) {
            $file = substr($file, strlen($path[0]));
        }

        // set the template diretory as the first directory from the path
        $this->_smarty->template_dir = $path[0];

        // build cache id by current url
        $cacheId = null;
        if ($request = Zend_Controller_Front::getInstance()->getRequest()) {
            /* @var $request Zend_Controller_Request_Http */
            $cacheId = md5($request->getRequestUri());
        }

        // process the template (and filter the output)
        echo $this->_smarty->fetch($file, $cacheId);
    }
}