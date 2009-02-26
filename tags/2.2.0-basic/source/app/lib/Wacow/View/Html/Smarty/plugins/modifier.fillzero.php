<?php
/**
 * Smarty plugin
 *
 * Fill zero
 *
 * Examples:
 * <code>
 * <% $num|fillzero:3 %>
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id$
 */

/**
 * smarty_modifier_fillzero
 *
 * @param int $num
 * @param int $length
 * @return string
 */
function smarty_modifier_fillzero($num, $length = 10)
{
    $numList = (array) $num;

    foreach ($numList as $key => $num) {
        $numList[$key] = sprintf('%0' . (int) $length . 'd', $num);
    }

    return implode(',', $numList);
}