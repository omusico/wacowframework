<?php
/**
 * Smarty plugin
 *
 * Show content if not accessible
 *
 * Examples:
 * <code>
 * Show disabled if not accessible.
 * <input type="button"
 * <% deny acl=$acl role="guest" resource="default:user" privilege="update" %>disabled="disabled"<% /deny %> />
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: block.deny.php 405 2008-04-20 02:38:02Z jaceju $
 */

/**
 * smarty_block_deny
 *
 * @param array $params
 * @param string $content
 * @param Smarty $smarty
 * @param bool $repeat
 * @return string
 */
function smarty_block_deny(array $params, $content, Smarty &$smarty, &$repeat)
{
    if (isset($content)) {

        require_once $smarty->_get_plugin_filepath('block', 'allow');

        if (!__checkAccess($params)) {
            return $content;
        }
    }
}
