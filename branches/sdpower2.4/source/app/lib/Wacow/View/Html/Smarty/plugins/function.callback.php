<?php
/**
 * Smarty plugin
 *
 * Invoke a callback function.
 *
 * Examples:
 * <code>
 * <% callback function="func_name" params=$params %>
 * <% callback class="class_name" method="method_name" params=$params %>
 * <% callback object=$object method="method_name" params=$params %>
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: function.array.php 405 2008-04-20 02:38:02Z jaceju $
 */

/**
 * smarty_function_callback
 *
 * @param array $params
 * @param Smarty $smarty
 */
function smarty_function_callback(array $params, Smarty &$smarty)
{
    $callback = null;
    $callee = null;
    if (isset($params['function']) && is_string($params['function'])) {
        $callee = trim($params['function']);
    } elseif (isset($params['class']) && class_exists($params['class'], true)) {
        $callee = $params['class'];
    } elseif (isset($params['object']) && is_object($params['object'])) {
        $callee = $params['object'];
    }

    if (!$callee) {
        $smarty->trigger_error("callback: missing 'function', 'class' or 'object' parameter", E_USER_ERROR, __FILE__, __LINE__);
        return;
    }

    if (!is_callable($callee) && !isset($params['method'])) {
        $smarty->trigger_error("callback: '$callee' is not callable or missing 'method' parameter", E_USER_ERROR, __FILE__, __LINE__);
        return;
    }

    $callback = $callee;
    if (!is_callable($callee) && isset($params['method'])) {
        $method = trim($params['method']);
        if (!is_callable(array($callee, $method))) {
            $smarty->trigger_error("callback: '$callee::$method' is not callable", E_USER_ERROR, __FILE__, __LINE__);
            return;
        }
        $callback = array($callee, $method);
    }

    $callbackParams = array();
    if (isset($params['params'])) {
        $callbackParams = (array) $params['params'];
    }

    $result = call_user_func_array($callback, $callbackParams);

    if (isset($params['assign']) && is_string($params['assign'])) {
        $smarty->assign(trim($params['assign']), $result);
    } else {
        return $result;
    }
}
