<?php
/**
 * Smarty plugin
 *
 * Truncate HTML.
 *
 * Examples:
 * <code>
 * <% $html|html_truncate:40 %>
 * </code>
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: modifier.html_truncate.php 476 2008-05-22 03:49:48Z jaceju $
 * @author      Jace Ju
 * @copyright   http://www.opensource.org/licenses/mit-license.php    The MIT License
 * @see         http://blog.stevenlevithan.com/archives/get-html-summary
 */

/**
 * smarty_modifier_html_truncate
 *
 * @param string $input
 * @param int $maxChrs
 * @return string
 */
function smarty_modifier_html_truncate($input, $maxChrs = 250) {
	// token matches a word, tag, or special character
	$token = '/\w+|[^\w<]|<(\/)?(\w+)[^>]*(\/)?>|</u';
	$matches = array();
	$output = '';
	$chrCount = 0;
	$openTags = array();
	$selfClosingTag = '/^(?:[hb]r|img)$/i';

    // Use preg_match_all instead of 'u' modifier
    preg_match_all($token, $input, $matches);

	for ($i = 0; $i < count($matches[0]); $i ++) {

        if ($chrCount >= $maxChrs) break;

		// If this is an HTML tag
		if (isset($matches[2][$i]) && $matches[2][$i]) {
			$output .= $matches[0][$i];
			// If this is not a self-closing tag
			if (!((isset($matches[3][$i]) && $matches[3][$i]) || preg_match($selfClosingTag, $matches[2][$i]))) {
				// If this is a closing tag
				if (isset($matches[1][$i]) && $matches[1][$i]) {
                    array_pop($openTags);
				} else {
					$openTags[] = $matches[2][$i];
				}
			}
		} else {
			$chrCount += mb_strlen($matches[0][$i], 'UTF-8');
			if ($chrCount <= $maxChrs) {
				$output .= $matches[0][$i];
			}
		}
	}

	// Close any tags which were left open
	for ($i = count($openTags) - 1; $i >= 0; $i--) {
		$output .= "</" . $openTags[$i] . ">";
	}

	return $output;
}
