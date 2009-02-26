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
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * Wacow_Application_Plugin_Timer
 *
 * @category   Wacow
 * @package    Wacow_Application
 * @subpackage Wacow_Application_Plugin
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Application_Plugin_Timer extends Wacow_Application_Plugin_Abstract
{
    /**
     * Start Time
     *
     * @var float
     */
    protected $_startTime = 0;

    /**
     * Set start time
     *
     */
    public function beforeRun()
    {
        $this->_startTime = (float) microtime(true);
    }

    /**
     * Get spent time
     *
     */
    public function afterRun()
    {
        $endTime = (float) microtime(true);
        $spentTime = $endTime - $this->_startTime;

        if ($response = $this->_app->getFrontController()->getResponse()) {
            $html = $response->getBody('LayoutManager');

            if (false !== strpos($html, '</html>')) {
                echo "\n<!-- Spent Time: $spentTime -->";
            }
        }
    }
}