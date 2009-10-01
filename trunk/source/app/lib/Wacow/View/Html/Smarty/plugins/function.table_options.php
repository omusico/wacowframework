<?php
/**
 * Smarty plugin
 *
 * Print options of selector from database table.
 *
 * Examples:
 * <code>
 * <% table_options database="sample" table="table" output="field1" values="field2" selected="value" where=$where order=$order %>
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: function.table_options.php 701 2009-09-04 12:57:08Z jaceju $
 */

/**
 * smarty_function_table_options
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_table_options(array $params, Smarty &$smarty)
{
    if (!isset($params['table'])) {
        $smarty->trigger_error("table_options: missing 'name' parameter", E_USER_ERROR, __FILE__, __LINE__);
        return;
    }
    $tableName = trim($params['table']);
    unset($params['table']);

    if (!isset($params['output'])) {
        $smarty->trigger_error("table_options: missing 'output' parameter", E_USER_ERROR, __FILE__, __LINE__);
        return;
    }
    $outputField = trim($params['output']);
    unset($params['output']);

    if (!isset($params['values'])) {
        $valueField = $outputField;
    } else {
        $valueField = trim($params['values']);
        unset($params['values']);
    }

    $dbName = isset($params['database'])
            ? trim($params['database'])
            : 'default';
    unset($params['database']);

    $where  = isset($params['where'])
            ? (array) $params['where']
            : null;
    unset($params['where']);

    $order  = isset($params['order'])
            ? (array) $params['order']
            : null;
    unset($params['order']);

    require_once $smarty->_get_plugin_filepath('function', 'html_options');

    $app = Wacow_Application::getInstance();

    $db = $app->getDbAdapter($dbName);

    $select = $db->select();
    $select->from($tableName, array('value' => $valueField, 'output' => $outputField));

    if ($where) {
        foreach ($where as $cond) {
            $select->where($cond);
        }
    }

    if ($order) {
        foreach ($order as $col) {
            $select->order($col);
        }
    }

    $result = $db->fetchPairs($select);

    $newParams = $params;

    if ($result) {
        $newParams = array_merge($newParams, array('options' => $result));
    }

    return smarty_function_html_options($newParams, $smarty);
}
