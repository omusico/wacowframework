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
 * @version    $Id: Abstract.php 418 2008-04-25 07:45:08Z jaceju $
 */

/**
 * Wacow_Application_Plugin_Abstract
 *
 * @category   Wacow
 * @package    Wacow_Application
 * @subpackage Wacow_Application_Plugin
 * @copyright
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Wacow_Application_Plugin_Abstract
{
    /**
     * Application object
     *
     * @var Wacow_Application
     */
    protected $_app = null;

    /**
     * Executing before application running
     *
     */
    public function beforeRun()
    {}

    /**
     * Executing after application running
     *
     */
    public function afterRun()
    {}

    /**
     * Set application object
     *
     * @param Wacow_Application $app
     */
    public function setApplication(Wacow_Application $app)
    {
        $this->_app = $app;
    }

    /**
     * Get plugin name
     *
     * @return string
     */
    public function getName()
    {
        $className = get_class($this);
        return strtolower(substr($className, strrpos($className, '_') + 1));
    }
}