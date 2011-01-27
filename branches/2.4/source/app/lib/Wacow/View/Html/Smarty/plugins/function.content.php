<?php
/**
 * Smarty plugin
 *
 * Display layout content or sub template (different than {include}).
 *
 * Examples:
 * <code>
 * Get content from action render
 * <wa:content name="contentFromAction" />
 *
 * Get content from template file
 * <% content name="otherContent" %>
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: function.content.php 613 2009-01-06 12:18:23Z jaceju $
 */

/**
 * smarty_function_content
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_content(array $params, Smarty &$smarty)
{
    static $commonTemplateDir = null;

    if (!isset($params['name'])) {
        $smarty->trigger_error("content: missing 'name' parameter", E_USER_ERROR, __FILE__, __LINE__);
        return;
    }

    // Get Content when content is exists.
    if (isset($smarty->_tpl_vars['layoutContents'][$params['name']])) {
        return $smarty->_tpl_vars['layoutContents'][$params['name']];
    }

    $view = $smarty->_tpl_vars['this'];
    /* @var $view Wacow_View */
    $viewSuffix = $view->getOption('viewSuffix');

    if (!$commonTemplateDir) {
        $commonTemplateDir = $view->getOption('commonTemplateDir');
    }

    // Load content from template like include file
    $filename = isset($viewSuffix)
              ? $params['name'] . '.' . $viewSuffix
              : $params['name'];
    unset($params['name']);

    // get common
    $filename = str_replace(':common', $commonTemplateDir, $filename);

    $_smarty_tpl_vars = $smarty->_tpl_vars;
    $smarty->_tpl_vars = array_merge($smarty->_tpl_vars, $params);

    // build cache id by current url
    $cacheId = null;
    if ($request = Zend_Controller_Front::getInstance()->getRequest()) {
        /* @var $request Zend_Controller_Request_Http */
        $cacheId = md5($request->getRequestUri());
    }

    $result = $smarty->fetch($filename, $cacheId);
    $smarty->_tpl_vars = $_smarty_tpl_vars;
    unset($_smarty_tpl_vars);
    return $result;
}
