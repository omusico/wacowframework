<?php
/**
 * Smarty plugin
 *
 * Load CSS.
 *
 * Examples:
 * <code>
 * Load custom style sheet which located /pub/css/admin/user.css
 * <wa:css href="/css/admin/user.css" />
 * convert to:
 * <link rel="stylesheet" type="text/css" href="/pub/css/admin/user.css" media="all" />
 *
 * Load style sheet of jquery thickbox for screen
 * <wa:css href="/lib/jquery/thickbox.css" media="screen" />
 * convert to:
 * <link rel="stylesheet" type="text/css" href="/pub/lib/jquery/thickbox.css" media="screen" />
 *
 * Load external javascript
 * <wa:css href="http://example.com/example.css" />
 * convert to:
 * <link rel="stylesheet" type="text/css" href="http://example.com/example.css" media="all" />
 *
 * Load IE only style sheet
 * <wa:css href="http://example.com/example.css" ie="yes" />
 * convert to:
 * <!--[if IE]>
 * <link rel="stylesheet" type="text/css" href="http://example.com/example.css" media="all" />
 * <![endif]-->
 *
 * Load javascript on IE which version less than 7
 * <wa:css href="/theme/wacow/style_ie.css" ie="yes" version="lt 7" />
 * <!--[if lt IE 7]>
 * <link rel="stylesheet" type="text/css" href="/pub/theme/wacow/style_ie.css" media="all" />
 * <![endif]-->
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: function.css.php 686 2009-06-09 04:08:25Z jaceju $
 */

/**
 * smarty_function_css
 *
 * @param array $params
 * @param Smarty $smarty
 */
function smarty_function_css(array $params, Smarty &$smarty)
{
    static $autoCompact = false;

    if (!isset($params['href'])) {
        $_smarty->trigger_error("css: missing 'href' parameter", E_USER_ERROR, __FILE__, __LINE__);
        return;
    }

    $view = $smarty->_tpl_vars['this'];

    // set variables
    $href     = trim($params['href']);
    $media    = (isset($params['media']) && $_tmp = trim(strtolower($params['media'])))
              ? $_tmp
              : 'all';
    $external = (bool) preg_match('/^(http|https):\/\//', $href);
    $ie       = (isset($params['ie']) && $_tmp = trim(strtolower($params['ie'])))
              ? ('yes' == $_tmp) ? 'IE' : null
              : 'none';
    $version  = isset($params['version'])
              ? trim(strtolower($params['version']))
              : null;
    $layoutType = ($view->layoutEnabled) ? 'sub' : 'main';

    if (!$autoCompact) {
        $app = Wacow_Application::getInstance();
        if (isset($app->getConfig('common')->asset->css->autoCompact)) {
            $autoCompact = (bool) $app->getConfig('common')->asset->css->autoCompact;
        }
    }

    // asset compact stylesheet support by racklin
    $compact  = isset($params['compact']) || $autoCompact;

    // layout
    if (!isset($view->loadedStyleSheets[$layoutType])) {
        $view->loadedStyleSheets[$layoutType] = array();
    }

    // process stylesheet for IE
    if ($ie && $version) {
        preg_match('/^([a-z]*)\s*([0-9]+)$/', $version, $matches);
        $ie = ($matches[1] ? $matches[1] . ' ' : '') . $ie . ' ' . $matches[2];
    }
    if (!isset($view->loadedStyleSheets[$layoutType][$ie])) {
        $view->loadedStyleSheets[$layoutType][$ie] = array();
    }

    // process compact stylesheet by racklin
    if ($compact && !isset($view->loadedStyleSheets[$layoutType]['compact'])) {
        $view->loadedStyleSheets[$layoutType]['compact'] = array();
    }

    // initital the path for stylesheet
    $baseUrl = $smarty->_tpl_vars['frontendVars']['baseUrl'];
    $pubWebPath = Wacow_Application::getInstance()->publicWebPath;

    // add stylesheet to compact array by racklin
    if ($compact && !$external) {

        // need src path
        $href = $baseUrl . rtrim($pubWebPath, '/') . $href;

        // key without baseUrl, but value with baseUrl for compatible
        $view->loadedStyleSheets[$layoutType]['compact'][$href] = $media;

        // break
        return;
    }

    // load specified file
    if (!isset($view->loadedStyleSheets[$layoutType][$ie][$href])) {
        if (!$external) {
            $href = $baseUrl . rtrim($pubWebPath, '/') . $href;
        }
        $view->loadedStyleSheets[$layoutType][$ie][$href] = $media;
    }
}