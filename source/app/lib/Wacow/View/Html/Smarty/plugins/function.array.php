<?php
/**
 * Smarty plugin
 *
 * Assign value to array.
 *
 * Examples:
 * <code>
 * <% array var="array1" key1="value1" key2="value2" %>
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: function.array.php 405 2008-04-20 02:38:02Z jaceju $
 */

/**
 * smarty_function_array
 *
 * @param array $params
 * @param Smarty $smarty
 */
function smarty_function_array(array $params, Smarty &$smarty)
{
    if (!isset($params['var'])) {
        $smarty->trigger_error("array: missing 'var' parameter", E_USER_ERROR, __FILE__, __LINE__);
        return;
    }

    $varname = $params['var'];
    if (isset($smarty->_tpl_vars[$varname]) && is_array($smarty->_tpl_vars[$varname])) {
        $var = $smarty->_tpl_vars[$varname];
    } else {
        $var = array();
    }

    unset($params['var']);

    $smarty->assign($varname, array_merge($var, $params));
}
