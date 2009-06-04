<?php
/**
 * Smarty plugin
 *
 * Alias str_replace function in PHP.
 *
 * Examples:
 * <code>
 * <% $html|replace:'search':'replace' %>
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id$
 * @author      Jace Ju
 * @copyright   http://www.opensource.org/licenses/mit-license.php    The MIT License
 */

/**
 * smarty_modifier_replace
 *
 * @param string $content
 * @param mixed $search
 * @param mixed $replace
 * @return string
 */
function smarty_modifier_replace($content, $search, $replace) {
    return str_replace($search, $replace, $content);
}
