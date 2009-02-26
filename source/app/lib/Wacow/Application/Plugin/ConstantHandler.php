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
 * @subpackage Wacow_Application_Plugin
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Wacow_Application_Plugin_Abstract
 */
require_once 'Wacow/Application/Plugin/Abstract.php';

/**
 * Wacow_Application_Plugin_ConstantHandler
 *
 * @category   Wacow
 * @package    Wacow_Application
 * @subpackage Wacow_Application_Plugin
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Application_Plugin_ConstantHandler extends Wacow_Application_Plugin_Abstract
{
    /**
     * Constants
     *
     * @var array
     */
    protected $_constants = array();

    /**
     * Executing before application running
     *
     */
    public function beforeRun()
    {
        $_appName   = strtoupper($this->_app->name);
        $_pattern   = '/^' . preg_quote($_appName) . '_/';
        $_constants = get_defined_constants(true);
        foreach ((array) $_constants['user'] as $key => $value) {
            if (preg_match($_pattern, $key)) {
                $this->_constants[$key] = $value;
            }
        }
    }

    /**
     * Get all defined constants
     *
     * @return array
     */
    public function getConstants()
    {
        return $this->_constants;
    }
}