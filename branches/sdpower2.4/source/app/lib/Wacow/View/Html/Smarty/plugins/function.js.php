<?php
/**
 * Smarty plugin
 *
 * Load JavaScript.
 *
 * Examples:
 * <code>
 * Load jquery library 1.2.1
 * <wa:js src="/lib/jquery/1_2_1.js" />
 * translate to:
 * <script type="text/javascript" src="/pub/lib/jquery/1_2_1.js"></script>
 *
 * Load custom javascript which located /pub/js/admin/edit_user.js
 * <wa:js src="/js/admin/edit_user.js" />
 * translate to:
 * <script type="text/javascript" src="/pub/js/admin/edit_user.js"></script>
 *
 * Load external javascript
 * <wa:js src="http://example.com/example.js" />
 * translate to:
 * <script type="text/javascript" src="http://example.com/example.js"></script>
 *
 * Load IE only javascript
 * <wa:js src="/js/admin/fix_layout.js" ie="yes" />
 * translate to:
 * <!--[if IE]>
 * <script type="text/javascript" src="/pub/js/admin/fix_layout.js"></script>
 * <![endif]-->
 *
 * Load javascript on IE which version less than 7
 * <wa:js src="/lib/ie7/ie7-standard-p.js" ie="yes" version="lt 7" />
 * <!--[if lt IE 7]>
 * <script type="text/javascript" src="/pub/lib/ie7/ie7-standard-p.js"></script>
 * <![endif]-->
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: function.js.php 686 2009-06-09 04:08:25Z jaceju $
 */

/**
 * smarty_function_js
 *
 * @param array $params
 * @param Smarty $smarty
 */
function smarty_function_js(array $params, Smarty &$smarty)
{
    static $app = null;
    static $defaultPosition = 'head';
    static $autoCompact = false;

    if (null === $app) {
        $app = Wacow_Application::getInstance();
        if (isset($app->getConfig('common')->javascript->defaultPosition)) {
            $defaultPosition = (string) $app->getConfig('common')->javascript->defaultPosition;
        }
        if (isset($app->getConfig('common')->asset->js->autoCompact)) {
            $autoCompact = (bool) $app->getConfig('common')->asset->js->autoCompact;
        }
    }

    if (!isset($params['src'])) {
        $smarty->trigger_error("js: missing 'src' parameter", E_USER_ERROR, __FILE__, __LINE__);
        return;
    }

    $view = $smarty->_tpl_vars['this'];

    // set variables
    $src      = trim($params['src']);
    $position = (isset($params['position'])
                    && ($_tmp = trim(strtolower($params['position'])))
                    && in_array($_tmp, array('head', 'body')))
              ? $_tmp : $defaultPosition;
    $external = (bool) preg_match('/^(http|https):\/\//', $src);
    $ie       = (isset($params['ie']) && $_tmp = trim(strtolower($params['ie'])))
              ? ('yes' == $_tmp) ? 'IE' : null
              : 'none';
    $version  = isset($params['version'])
              ? trim(strtolower($params['version']))
              : null;
    $layoutType = ($view->layoutEnabled) ? 'sub' : 'main';

    // layaout
    if (!isset($view->loadedScripts[$layoutType])) {
        $view->loadedScripts[$layoutType] = array();
    }

    // position
    if (!isset($view->loadedScripts[$layoutType][$position])) {
        $view->loadedScripts[$layoutType][$position] = array();
    }

    // process scripts
    // asset compact javascript support by racklin
    $compact = isset($params['compact']) || $autoCompact;

    // process javascript for IE
    if ($ie && $version) {
        preg_match('/^([a-z]*)\s*([0-9]+)$/', $version, $matches);
        $ie = ($matches[1] ? $matches[1] . ' ' : '') . $ie . ' ' . $matches[2];
    }
    if (!isset($view->loadedScripts[$layoutType][$position][$ie])) {
        $view->loadedScripts[$layoutType][$position][$ie] = array();
    }

    // process compact javascript by racklin
    if ($compact && !isset($view->loadedScripts[$layoutType][$position]['compact'])) {
        $view->loadedScripts[$layoutType][$position]['compact'] = array();
    }

    // initital the path for javascript
    $baseUrl = $smarty->_tpl_vars['frontendVars']['baseUrl'];
    $pubWebPath = Wacow_Application::getInstance()->publicWebPath;

    // add javascript to compact array by racklin
    if ($compact && !$external) {

        // need src path without baseUrl
        $src = rtrim($pubWebPath, '/') . $src;

        // key without baseUrl, but value with baseUrl for compatible
        $view->loadedScripts[$layoutType][$position]['compact'][$src] = $baseUrl . $src;

        // break
        return;
    }

    // load specified file
    if (!isset($view->loadedScripts[$layoutType][$position][$ie][$src])) {
        if (!$external) {
            $src = $baseUrl . rtrim($pubWebPath, '/') . $src;
        }
        $view->loadedScripts[$layoutType][$position][$ie][$src] = $src;
    }
}