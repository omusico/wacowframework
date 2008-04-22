<?php
/**
 * Smarty plugin
 *
 * Inject the css and js tag to html head.
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: outputfilter.html.php 405 2008-04-20 02:38:02Z jaceju $
 */

/**
 * smarty_outputfilter_html
 *
 * @param string $output
 * @param Smarty $smarty
 * @return string
 */
function smarty_outputfilter_html($output, Smarty &$smarty)
{
    $result = '';
    $view = $smarty->_tpl_vars['this'];

    // process css
    $loadedStyleSheets = $view->loadedStyleSheets;
    foreach ((array) $loadedStyleSheets as $browserVersion => $srcs) {
        if ('none' !== $browserVersion) {
            $result .= "<!--[if $browserVersion]>\n";
        }
        foreach ($srcs as $href => $media) {
            $result .= '<link rel="stylesheet" type="text/css" href="' . $href . '" media="' . $media . '" />' . "\n";
        }
        if ('none' !== $browserVersion) {
            $result .= "<![endif]-->\n";
        }
    }

    // process constants
    $result .= '<script type="text/javascript">' . "\n";
    $frontendVars = (array) $smarty->_tpl_vars['frontendVars'];
    $vars = array();
    foreach ($frontendVars as $name => $value) {
        if (is_string($frontendVars[$name])) {
            $vars[$name] = $value;
        }
    }
    foreach ((array) $frontendVars['constants'] as $name => $value) {
        $vars[$name] = $value;
    }
    $result .= 'var $frontendVars = ' . Zend_Json::encode($vars) . ";\n";
    $result .= "</script>\n";

    // process javascript
    $loadedScripts = $view->loadedScripts;
    foreach ((array) $loadedScripts as $browserVersion => $srcs) {
        if ('none' !== $browserVersion) {
            $result .= "<!--[if $browserVersion]>\n";
        }
        foreach ($srcs as $src) {
            $result .= '<script type="text/javascript" src="' . $src . '"></script>' . "\n";
        }
        if ('none' !== $browserVersion) {
            $result .= "<![endif]-->\n";
        }
    }

    $pattern = '/(<\/head>)/i';
    $replace = "$result\n\\1";

    $output = preg_replace($pattern, $replace, $output);

    return $output;
}