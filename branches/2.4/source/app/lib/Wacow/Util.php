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

    /**
     * Create directory
     *
     * @param string $path
     * @return string
     */
    public static function createDir($path)
    {
        if (!is_dir($path)) {
            mkdir($path);
        }

        return $path;
    }

    /**
     * Fill zero
     *
     * @param mixed $num
     * @param int $length
     * @return string
     */
    public static function fillZero($num, $length = 10)
    {
        $numList = (array) $num;

        foreach ($numList as $key => $num) {
            $numList[$key] = sprintf('%0' . (int) $length . 'd', $num);
        }

        return implode(',', $numList);
    }

    /**
     * Translate value to path
     *
     * @param string $val
     * @param int $length
     * @param string $glue
     * @return string
     */
    public function transToPath($val, $length = null, $glue = null)
    {
        if (0 < (int) $length) {
            $val = self::fillZero($val, $length);
        }

        $result = preg_split('//', (string) $val);
        unset($result[0]);
        unset($result[count($result)]);

        $max = count($result);
        if (null === $length || 0 >= $length) {
            $max = 0;
        } else {
            $max -= $length;
        }
        for ($i = 1; $i <= $max; $i ++) {
            unset($result[$i]);
        }
        return (null === $glue) ? array_values($result) : join($glue, $result);
    }

    /**
     * Get Client IP
     *
     * @return string
     */
    public static function getClientIP()
    {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        return $ip;
    }

    /**
     * Formats a string from a URI into a PHP-friendly name.
     *
     * By default, replaces words separated by the word separator character(s)
     * with camelCaps. If $isAction is false, it also preserves replaces words
     * separated by the path separation character with an underscore, making
     * the following word Title cased. All non-alphanumeric characters are
     * removed.
     *
     * @param string $unformatted
     * @return string
     */
    public static function normalizeClassName($unformatted)
    {
        // preserve directories
        $segments = explode('-', $unformatted);

        foreach ($segments as $key => $segment) {
            $segment        = preg_replace('/[^a-z0-9 ]/', '', $segment);
            $segments[$key] = str_replace(' ', '', ucwords($segment));
        }

        return implode('_', $segments);
    }
}

