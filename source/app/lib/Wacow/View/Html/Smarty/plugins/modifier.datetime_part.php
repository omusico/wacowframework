<?php
/**
 * Smarty plugin
 *
 * Output formated date or time.
 *
 * Examples:
 * <code>
 * <% $createDateTime|datetime_part:'date' %>
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id$
 */

/**
 * smarty_modifier_datetime_part
 *
 * @param string $string
 * @param string $part
 * @return string
 */
function smarty_modifier_datetime_part($string, $part)
{
    $part = strtoupper($part);
    if (!$string) {
        return null;
    }
    $dateTimeParts = date_parse($string);
    if ('DATE' === $part) {
        return sprintf('%04d-%02d-%02d', $dateTimeParts['year'], $dateTimeParts['month'], $dateTimeParts['day']);
    } elseif ('TIME' === $part) {
        return sprintf('%02d-%02d-%02d', $dateTimeParts['hour'], $dateTimeParts['minute'], $dateTimeParts['second']);
    } else {
        return '';
    }
}