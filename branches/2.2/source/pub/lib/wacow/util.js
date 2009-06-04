/**
 * jQuery Wacow utilities
 * Version 2.2 (12/22/2008)
 * @requires jQuery v1.2.6 or later
 *
 * Copyright (c) 2007-2008 M. Alsup
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */
;(function ($) {
$.wacow = $.wacow || {};
$.extend($.wacow, {
//=====================================================

/**
 * Get document object of iframe
 *
 * @param object iframeObject
 */
getIframeDocument: function (iframeObject) {
    if (iframeObject.contentWindow) {
        return iframeObject.contentWindow.document;
    } else if (iframeObject.contentDocument) {
        return iframeObject.contentDocument.document;
    }
    return null;
},

/**
 * Display message at bottom of document
 *
 * @param mixed m
 */
vardump: function (m) {
    var s = '';
    switch (typeof(m)) {
        case 'object':
        case 'array':
            for (var i in m) {
                s += i + ' = ' + m[i] + '<br />';
            }
            break;
        default:
            s = m;
            break;
    }
    $('body').append('<div id="wacowMessageBlock">' + s + '</div>');
},

/**
 * Return if full-width char
 *
 */
isFullWidth: function (str) {
    str = String(str).replace(/%u/g, "");
    var eStr = escape(str);
    if (eStr.indexOf("%u") == -1) {
        return false;
    } else {
        return true;
    }
},

/**
 * Transform to helf-width
 *
 */
toHelfWidth: function (str) {
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
},

/**
 * Transform to full-width
 *
 */
toFullWidth: function (str) {
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
},

/**
 * Get the length of string (helf as 1, full as 2)
 *
 */
getFullWidthLen: function (str) {
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
},

/**
 * Clipboard
 *
 * @author Jace Ju
 * @see http://www.jeffothy.com/weblog/clipboard-copy/
 * @example
 * <code>
 * $.wacow.copy('#inputId', 'elements');
 * </code>
 *
 * @example
 * <code>
 * var settings = { flashBasePath: '/pub/flash', event: 'click' };
 * $.wacow.copy('#inputId', 'elements', settings);
 * </code>
 */
copy: function (inputId, elementsExpr, settings) {

    var _settings = {
        flashBasePath: '',
        event: 'click',
        beforeCopy: function () {},
        afterCopy: function () {}
    };

    jQuery.extend(_settings, settings);

    var inElement = $(inputId)[0];

    $(elementsExpr).bind(_settings.event, function () {
        _settings.beforeCopy.apply(this);
        if (inElement.createTextRange) {
            var range = inElement.createTextRange();
            if (range) range.execCommand('Copy');
        } else {
            var flashcopier = 'flashcopier';
            if (!document.getElementById(flashcopier)) {
                var divholder = document.createElement('div');
                divholder.id = flashcopier;
                document.body.appendChild(divholder);
            }
            document.getElementById(flashcopier).innerHTML = '';
            var divinfo = '<embed src="' + _settings.flashBasePath + '_clipboard.swf" FlashVars="clipboard=' + encodeURIComponent(inElement.value) + '" width="0" height="0" type="application/x-shockwave-flash"></embed>';
            document.getElementById(flashcopier).innerHTML = divinfo;
        }
        _settings.afterCopy.apply(this);
    });
}

//=====================================================
});
})(jQuery);

/**
 *
 * @see http://www.zeali.net/entry/561
 */
jQuery.extend({
    unselectContents: function() {
        if(window.getSelection)
            window.getSelection().removeAllRanges();
        else if(document.selection)
            document.selection.empty();
    }
});

jQuery.fn.extend({
    selectContents: function() {
        return $(this).each(function(i) {
            var node = this;
            var selection, range, doc, win;
            if ((doc = node.ownerDocument) &&
                (win = doc.defaultView) &&
                typeof win.getSelection != 'undefined' &&
                typeof doc.createRange != 'undefined' &&
                (selection = window.getSelection()) &&
                typeof selection.removeAllRanges != 'undefined')
            {
                range = doc.createRange();
                range.selectNode(node);
                if(i == 0) {
                    selection.removeAllRanges();
                }
                selection.addRange(range);
            }
            else if (document.body &&
                     typeof document.body.createTextRange != 'undefined' &&
                     (range = document.body.createTextRange()))
            {
                range.moveToElementText(node);
                range.select();
            }
        });
    },

    setCaret: function() {
        if (!$.browser.msie) return;
        var initSetCaret = function() {
            var textObj = $(this).get(0);
            textObj.caretPos = document.selection.createRange().duplicate();
        };
        return $(this)
        .click(initSetCaret)
        .select(initSetCaret)
        .keyup(initSetCaret);
    },

    insertAtCaret: function(textFeildValue) {
       var textObj = $(this).get(0);
       if(document.all && textObj.createTextRange && textObj.caretPos) {
           var caretPos = textObj.caretPos;
           caretPos.text = caretPos.text.charAt(caretPos.text.length-1) == '' ?
                               textFeildValue + '' : textFeildValue;
       }
       else if(textObj.setSelectionRange) {
           var rangeStart = textObj.selectionStart;
           var rangeEnd = textObj.selectionEnd;
           var tempStr1 = textObj.value.substring(0,rangeStart);
           var tempStr2 = textObj.value.substring(rangeEnd);
           textObj.value = tempStr1 + textFeildValue + tempStr2;
           textObj.focus();
           var len = textFeildValue.length;
           textObj.setSelectionRange(rangeStart + len, rangeStart + len);
           textObj.blur();
       }
       else {
           textObj.value += textFeildValue;
       }
       return $(this);
    }
});