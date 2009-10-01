<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @package     App
 * @copyright
 * @version     $Id$
 */

/**
 * Utilities
 *
 * @category    %ProjectName%
 * @package     App
 * @copyright
 */
class App_Util
{
    /**
     * Check Taiwan Personal Identity
     *
     * @param string $identity
     * @return bool
     */
    public static function isTaiwanPersonalIdentity($identity)
    {
        // code of city
        $city = array(
            'A' => 1,  'I' => 39, 'O' => 48, 'B' => 10,
            'C' => 19, 'D' => 28, 'E' => 37, 'F' => 46,
            'G' => 55, 'H' => 64, 'J' => 73, 'K' => 82,
            'L' => 2,  'M' => 11, 'N' => 20, 'P' => 29,
            'Q' => 38, 'R' => 47, 'S' => 56, 'T' => 65,
            'U' => 74, 'V' => 83, 'W' => 21, 'X' => 3,
            'Y' => 12, 'Z' => 30
        );
        // check format
        if (!preg_match("/[A-Z][1-2]\d{8}/", $identity = strtoupper($identity))) {
            return false;
        } else {
            // sum
            $total = $city[$identity[0]];
            for ($i = 1; $i <= 8; $i ++) {
                $total += $identity[$i] * (9 - $i);
            }
            // add check code
            $total += $identity[9];
            // check sum
            return ($total % 10 === 0);
        }
    }

    /**
     * Check Taiwan VatNumber
     *
     * @param string $vatNumber
     * @return bool
     */
    public static function isTaiwanVatNumber($vatNumber)
    {
        $vatNumber = (string) $vatNumber;

        $weight = '12121241';
        $sum = 0;
        $type = false;

        // check format
        if (!preg_match("/^\d{8}$/", $vatNumber)) {
            return false;
        } else {
            for ($i = 0; $i < 8; $i++) {
            $tmp = (int) $vatNumber[$i] * $weight[$i];
            $sum += floor($tmp / 10) + ($tmp % 10);
                if ($i == 6 && $vatNumber[$i] == '7') {
                    $type = true;
                }
            }
            if ($type) {
                if (($sum % 10) == 0 || (($sum + 1) % 10) == 0) {
                    return true;
                }
            } else {
                if (($sum % 10) == 0) {
                    return true;
                }
            }
        }
    }

    /**
     * Get Server IP
     *
     * @return string
     */
    public static function getServerIP()
    {
        return $_SERVER['SERVER_ADDR'];
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
     * Get Client Agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Debug
     *
     * @param mixed $value
     */
    public static function debug($name, $value, $showTime = true)
    {
        $filePath = WF_APP_PATH . '/log/dump/' . date('Ymd') . '.log';
        $content = (($showTime ? date('Y-m-d H:i:s') : '') . "\n") . $name . "\n" . print_r($value, true) . "\n\n";
        file_put_contents($filePath, $content, FILE_APPEND);
    }
}