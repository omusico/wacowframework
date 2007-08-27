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
 * @package    Wacow_Pagination
 * @copyright
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Class for pagination to dataset or model common operations.
 *
 * @category   Wacow
 * @package    Wacow_Pagination
 * @copyright
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
final class Wacow_Pagination
{
    public final static function getPager(array $setting = array(), $type = '')
    {
        $className = 'Wacow_Pagination_Pager';
        $type      = ucfirst(strtolower($type));
        if (class_exists($className . '_' . $type, true)) {
            $className .= '_' . $type;
        }
        return new $className($setting);
    }
}