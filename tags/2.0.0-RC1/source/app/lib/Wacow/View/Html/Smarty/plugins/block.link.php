<?php
/**
 * Smarty plugin
 *
 * Build html link tag with baseUrl.
 *
 * Examples:
 * <code>
 * local href
 * <wa:link href="/download">Download</wa:link>
 *
 * keep current href and add/replace some params
 * <wa:link params="params">Page 1</wa:link>
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id$
 */

/**
 * smarty_block_link
 *
 * @param array $params
 * @param string $content
 * @param Smarty $smarty
 * @param bool $repeat
 * @return string
 */
function smarty_block_link(array $params, $content, Smarty &$smarty, &$repeat)
{
    if (isset($content)) {

        require_once $smarty->_get_plugin_filepath('function', 'url');
        $href = smarty_function_url($params, $smarty);
        unset($params['href']);
        unset($params['router']);
        unset($params['reset']);
        unset($params['encode']);
        unset($params['params']);

        $otherAttributes = '';
        foreach ($params as $key => $value) {
            $otherAttributes .= ' ' . $key . '="' . $value . '"';
        }

        return '<a href="' . $href . '"' . $otherAttributes . '>' . $content . '</a>';
    }
}
