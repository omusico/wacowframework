/**
 * Check is it full-width?
 *
 * Example:
 * <code>
 * </code>
 * @author Jace Ju
 * @see http://dinghaw.blogspot.com/2007/03/javascript.html
 * @license MIT License
 */
jQuery.isFullWidth = function (str) {
    str = String(str).replace(/%u/g, "");
    var eStr = escape(str);
    if (eStr.indexOf("%u") == -1) {
        return false;
    } else {
        return true;
    }
};

/**
 * Transform to helf-width
 *
 */
jQuery.toHelfWidth = function (str) {
    var result = '';
    var str = String(str);
    for (i = 0; i < str.length; i ++) {
        if(jQuery.isFullWidth(str[i])) {
            result += String.fromCharCode(str.charCodeAt(i) - 0xfee0);
        } else {
            result += str.charAt(i);
        }
    }
    return result;
};

/**
 * Transform to full-width
 *
 */
jQuery.toFullWidth = function (str) {
    var result = '';
    var str = String(str);
    for (i = 0; i < str.length; i ++) {
        if(jQuery.isFullWidth(str[i])) {
            result += str.charAt(i);
        } else {
            result += String.fromCharCode(str.charCodeAt(i) + 0xfee0);
        }
    }
    return result;
};

/**
 * Get the length of string (helf as 1, full as 2)
 *
 */
jQuery.getFullWidthLen = function (str) {
    var len = 0;
    var str = String(str);
    for (i = 0; i < str.length; i ++) {
        if(jQuery.isFullWidth(str[i])) {
            len += 2;
        } else {
            len += 1;
        }
    }
    return len;
};