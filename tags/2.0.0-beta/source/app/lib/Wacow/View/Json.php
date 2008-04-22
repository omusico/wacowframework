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
 * @version    $Id: Json.php 406 2008-04-20 02:43:18Z jaceju $
 */

/**
 * @see Wacow_View
 */
require_once 'Wacow/View.php';


/**
 * @category   Wacow
 * @package    Wacow_View
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_View_Json extends Wacow_View
{
    /**
     * No renders any template so returns nothing.
     *
     * @param string $name The script script name to process.
     * @return null
     */
    public function _script($name)
    {
        return null;
    }

    /**
     * Output the variables of view object by json format
     *
     */
    protected function _run()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $var) {
            if (0 === strpos($key, '_')) {
                unset($vars[$key]);
            }
        }
        echo Zend_Json::encode($vars);
    }
}