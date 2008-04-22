<?php
/**
 * Smarty plugin
 *
 * Get a url with baseUrl.
 *
 * Examples:
 * <code>
 * local href
 * <% url href="/download" %>
 *
 * keep current href and add/replace some params
 * <% url params="params" %>
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: function.url.php 405 2008-04-20 02:38:02Z jaceju $
 */

/**
 * smarty_function_url
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_url(array $params, Smarty &$smarty)
{
    static $baseUrl = null;

    if ($baseUrl === null) {
        $root = '/' . trim($smarty->_tpl_vars['frontendVars']['baseUrl'], '/');
        if ($root == '/') $root = '';
        $baseUrl = $root . '/';
    }

    $href       = isset($params['href'])
                ? $params['href']
                : null;

    $router     = isset($params['router'])
                ? $params['router']
                : null;

    $reset      = isset($params['reset'])
                ? $params['reset']
                : false;

    $encode     = isset($params['encode'])
                ? $params['encode']
                : true;

    $noBaseUrl  = isset($params['noBaseUrl']) && ('yes' == strtolower($params['noBaseUrl']))
                ? true
                : false;

    $issetParamsName = isset($params['params'])
                    && is_string($params['params'])
                    && isset($smarty->_tpl_vars[$params['params']])
                    && is_array($smarty->_tpl_vars[$params['params']]);
    $urlParams = $issetParamsName
               ? $smarty->_tpl_vars[$params['params']]
               : array();

    $urlParams = (!$urlParams && isset($params['params']) && is_array($params['params']))
               ? $params['params']
               : $urlParams;

    if ($href) {
        if (!preg_match('/^([a-z]+):\/\//i', $href) && !preg_match('/^\#/', $href)) {
            $href = ($noBaseUrl) ? $href : $baseUrl . ltrim($href, '/');
            if (null !== $urlParams) {
                $paramPairs = array();
                foreach ($urlParams as $key => $value) {
                    if (null != $value) {
                        $paramPairs[] = urlencode($key) . '/' . urlencode($value);
                    }
                }
                $paramString = implode('/', $paramPairs);
                if ('' != $paramString) {
                    $href .= '/' . $paramString;
                }
            }
        }
    } else {
        // Resolve the problem of urlencode.
        $currentUrl = $smarty->_tpl_vars['this']->url($urlParams, $router, $reset, $encode);
        $url = ($noBaseUrl)
             ? '/' . ltrim(preg_replace('/^' . preg_quote($baseUrl, '/') . '/', '', $currentUrl), '/')
             : $currentUrl;
        $tempUrlList = split('/', $url);
        foreach ($tempUrlList as $key => $value) {
        	$tempUrlList[$key] = urlencode($value);
        }
        $href = implode('/', $tempUrlList);
    }

    return $href;
}
