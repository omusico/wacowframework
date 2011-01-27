<?php
/**
 * Smarty plugin
 *
 * Show content if accessible
 *
 * Examples:
 * <code>
 * Show update link if accessible.
 * <% allow acl=$acl role="guest" resource="default:user" privilege="update" admin=$smart.const.ADMIN %><a href="#">Update</a><% /allow %>
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: block.allow.php 405 2008-04-20 02:38:02Z jaceju $
 */

/**
 * smarty_block_allow
 *
 * @param array $params
 * @param string $content
 * @param Smarty $smarty
 * @param bool $repeat
 * @return string
 */
function smarty_block_allow(array $params, $content, Smarty &$smarty, &$repeat)
{
    if (isset($content)) {
        if (_checkAccess($params)) {
            return $content;
        }
    }
}

/**
 * Check accessible
 *
 * @see smarty_block_allow
 * @param array $params
 * @return boolean
 */
function _checkAccess(Array $params)
{
    if (!isset($params['acl'])) {
        $_smarty->trigger_error("access: missing 'acl' parameter", E_USER_ERROR, __FILE__, __LINE__);
        return;
    }

    if (!isset($params['role'])) {
        $_smarty->trigger_error("access: missing 'role' parameter", E_USER_ERROR, __FILE__, __LINE__);
        return;
    }

    if (!isset($params['resource'])) {
        $_smarty->trigger_error("access: missing 'resource' parameter", E_USER_ERROR, __FILE__, __LINE__);
        return;
    }

    // set variables
    $acl       = $params['acl'];
    $role      = $params['role'];
    $resource  = $params['resource'];
    $privilege = isset($params['privilege'])
               ? trim($params['privilege'])
               : null;
    $admin     = isset($params['admin'])
               ? trim($params['admin'])
               : 1;


    // check acl object
    if (!($acl instanceof Zend_Acl)) {
        $_smarty->trigger_error("access: It is not a Zend_Acl object from 'acl' parameter", E_USER_ERROR, __FILE__, __LINE__);
    }
    /* @var $acl Zend_Acl */

    // check access
    try {
        return ($privilege)
             ? $acl->isAllowed($role, $resource, $privilege)
             : $acl->isAllowed($role, $resource);
    } catch (Exception $e) {
        return ($admin == $role) ? true : false;
    }
}