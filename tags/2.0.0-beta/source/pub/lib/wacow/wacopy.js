/**
 * Clipboard plugin
 *
 *   jQuery plugin for clipboard
 *
 * @author Jace Ju
 * @see http://www.jeffothy.com/weblog/clipboard-copy/
 * @license MIT License
 * @example
 * <code>
 * $.wacopy('#inputId', 'elements');
 * var settings = { flashBasePath: '/pub/flash', event: 'click' };
 * $.wacopy('#inputId', 'elements', settings);
 * </code>
 */

;

jQuery.wacopy = function (inputId, elementsExpr, settings) {

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
};