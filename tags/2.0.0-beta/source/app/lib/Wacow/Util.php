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
 * @package    Wacow_Util
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @category   Wacow
 * @package    Wacow_Util
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Util
{
    /**
     * Convert a shorthand byte value from a PHP configuration directive to an integer value
     *
     * Example:
     * <code>
     * echo Wacow_Util::convertBytes('2M');
     * echo Wacow_Util::convertBytes('10K');
     * echo Wacow_Util::convertBytes('120G');
     * </code>
     * @param string $value
     * @return int
     */
    public static function convertBytes($value)
    {
        if (is_numeric($value)) {
            return $value;
        } else {
            $value_length = strlen($value);
            $qty = substr($value, 0, $value_length - 1);
            $unit = strtolower(substr($value, $value_length - 1));
            switch ($unit) {
                case 'k':
                    $qty *= 1024;
                    break;
                case 'm':
                    $qty *= 1048576;
                    break;
                case 'g':
                    $qty *= 1073741824;
                    break;
            }
            return $qty;
        }
    }

    /**
     * Recursive array_map
     *
     * Example:
     * <code>
     * $a = array (1, 2, 3, 4, 5);
     * $a = Wacow_Util::arrayMapRecursive('function_name', $a); // callback function
     * $a = Wacow_Util::arrayMapRecursive(array('class_name', 'class_method'), $a); // callback static class method
     * $a = Wacow_Util::arrayMapRecursive(array($object, 'object_method'), $a); // callback object method
     * </code>
     * @param callback $func
     * @param array $arr
     */
    public function arrayMapRecursive($func, $arr)
    {
        $result = array();
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::arrayMapRecursive($func, $value);
            }
            else {
                if (is_array($func)) {
                    if (is_object($func[0]))
                        $result[$key] = $func[0]->$func[1]($value);
                    if (is_string($func[0]))
                        eval('$result[$key] = ' . $func[0] . '::' . $func[1] . '($value);');
                } else {
                    $result[$key] = $func($value);
                }
            }
        }
        return $result;
    }

    /**
     * Regexp Quote for MySQL
     *
     * @param string $str
     * @return string
     */
    public function mysqlRegexpQuote($str)
    {
        $singles = array_map('preg_quote', array(
            '.', '\\', '+', '*', '?', '[',
            '^', ']',  '$', '(', ')', '{',
            '}', '=',  '!', '<', '>', '|'));
        $delimiter = '/';
        $pattern = $delimiter . '(' . join('|', $singles) . ')' . $delimiter;
        $replace = '[\1]';
        return preg_replace($pattern, $replace, $str);
    }

    /**
     * Convert the value from tag
     *
     * @param string $value
     * @return mixed
     */
    public static function convertValue($value, $type = null)
    {
        switch ($type) {
        	case 'array':
        		return array_map('trim', split(',', $value));
        		break;
        	case 'boolean':
        	    if ('false' == strtolower(trim($value))) {
                    return false;
                }
                if ('true' == strtolower(trim($value))) {
                    return true;
                }
        	default:
                if (preg_match('/^[0-9]+$/', $value)) {
                    return (int) $value;
                }
                return trim($value);
        		break;
        }
    }
}

