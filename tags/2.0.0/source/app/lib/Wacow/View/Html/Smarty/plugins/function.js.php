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
 * @version     $Id: function.js.php 405 2008-04-20 02:38:02Z jaceju $
 */

/**
 * smarty_function_js
 *
 * @param array $params
 * @param Smarty $smarty
 */
function smarty_function_js(array $params, Smarty &$smarty)
{
    $view = $smarty->_tpl_vars['this'];

    if (!isset($params['src'])) {
        $smarty->trigger_error("js: missing 'src' parameter", E_USER_ERROR, __FILE__, __LINE__);
        return;
    }

    // set variables
    $src      = trim($params['src']);
    $external = (bool) preg_match('/^(http|https):\/\//', $src);
    $ie       = (isset($params['ie']) && $_tmp = trim(strtolower($params['ie'])))
              ? ('yes' == $_tmp) ? 'IE' : null
              : 'none';
    $version  = isset($params['version'])
              ? trim(strtolower($params['version']))
              : null;

    // process javascript for IE
    if ($ie && $version) {
        preg_match('/^([a-z]+)\s+([0-9]+)$/', $version, $matches);
        $ie = $matches[1] . ' ' . $ie . ' ' . $matches[2];
    }
    if (!isset($view->loadedScripts[$ie])) {
        $view->loadedScripts[$ie] = array();
    }

    // initital the path for javascript
    $baseUrl = $smarty->_tpl_vars['frontendVars']['baseUrl'];
    $pubPath = Wacow_Application::getInstance()->publicPath;

    // load specified file
    if (!isset($view->loadedScripts[$ie][$src])) {
        if (!$external) {
            $src = $baseUrl . _getSrcWithVersion(rtrim($pubPath, '/') . $src);
        }
        $view->loadedScripts[$ie][$src] = $src;
    }
}

/**
 * Get file version
 *
 * @see smarty_function_js
 * @param string $src
 * @return string
 */
function _getSrcWithVersion($src)
{
    // add the modified time
    $app = Wacow_Application::getInstance();
    $filePath  = $app->translatePath(':rootPath') . $src;
    $debugMode = $app->debugMode;
    $modifyDateTime = !$debugMode && file_exists($filePath) ? filemtime($filePath) : time();
	$q = '&';
	if (strpos($src, '?') === false) $q = '?';
    return $src . $q . 'v=' . $modifyDateTime;
}