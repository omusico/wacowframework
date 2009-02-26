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
 *
 * assign current href to a variable without baseUrl
 * <% url assign=currentUrl noBaseUrl=yes %>
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: function.url.php 455 2008-05-14 07:21:08Z jaceju $
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

    $assign     = isset($params['assign']) ? trim($params['assign'])  : null;

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

    if ($assign) {
        $smarty->assign($assign, $href);
    } else {
        return $href;
    }
}
