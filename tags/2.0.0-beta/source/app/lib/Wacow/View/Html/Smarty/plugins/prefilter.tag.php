<?php
/**
 * Smarty plugin
 *
 * Convert Wacow tags to smarty tags.
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: prefilter.tag.php 405 2008-04-20 02:38:02Z jaceju $
 */

/**
 * smarty_prefilter_tag
 *
 * @param string $source
 * @param Smarty $smarty
 * @return string
 */
function smarty_prefilter_tag($source, Smarty &$smarty)
{
    $patterns = array(
        '/<wa:(\w+)([^>]*)>/',
        '/<\/wa:(\w+)>/',
        '/\/' . $smarty->right_delimiter . '/',
    );
    $replace  = array(
        $smarty->left_delimiter . '\1\2' . $smarty->right_delimiter,
        $smarty->left_delimiter . '/\1' . $smarty->right_delimiter,
        $smarty->right_delimiter,
    );
    return preg_replace($patterns, $replace, $source);
}