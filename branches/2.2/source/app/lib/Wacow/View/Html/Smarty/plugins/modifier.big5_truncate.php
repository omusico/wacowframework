<?php
/**
 * Smarty plugin
 *
 * Truncate HTML.
 *
 * Examples:
 * <code>
 * <% $input|big5_truncate:40 %>
 * <% $input|big5_truncate:40:'UTF-8' %>
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id$
 * @author      Jace Ju
 * @copyright   http://www.opensource.org/licenses/mit-license.php    The MIT License
 */

/**
 * smarty_modifier_big5_truncate
 *
 * @param string $input
 * @param int $maxChars
 * @param string $origCharset
 * @return string
 */
function smarty_modifier_big5_truncate($input, $maxChars = 250, $origCharset = null) {

    if ($origCharset) {
        $input = mb_convert_encoding($input, 'BIG5', $origCharset);
    }

    $maxChars = (int) $maxChars;
    $len      = strlen($input);

    if ($len > $maxChars) {

        $i      = 0;
        $index  = 1;
        $output = '';
        $char   = '';

        while ($i < $maxChars) {

            $char = substr($input, $i, 1);

            if (ord($char) > 127) { // chinese
                $output = $output . $char . substr($input, ($i + 1), 1);
                $i += 2;
            } else { // numeric or alphabet
                $output .= $char;
                $i ++;
            }
        }

    } else {
        $output = $input;
    }

    if ($origCharset) {
        $output = mb_convert_encoding($output, $origCharset, 'BIG5');
    }

    return $output;
}