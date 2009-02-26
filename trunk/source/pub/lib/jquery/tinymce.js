/**
 * TinyMCE plugin
 *
 *   jQuery plugin for accessible, unobtrusive WYSIWYG HTML editing
 *   base on <tiny MCE> by <Alton Crossley> but settings can be reloaded
 *
 * Example:
 * <code>
 * $(function() {
 *     $('.mceedit').tinymce();
 * });
 * $.tinymce.init( { settings } );
 * </code>
 * @author Jace Ju
 * @see http://www.nogahidebootstrap.com/jtinymce/
 * @license MIT License
 */

;

if (tinyMCE) {
    jQuery.tinymce = {
        settings: {
            mode: 'none',
            theme: 'simple'
        },
        init: function (settings) {
            if (settings) { jQuery.tinymce.settings = settings; }
            tinyMCE.init(jQuery.tinymce.settings);
        },
        controls: []
    };
}

(function($) { // plugin body start

var $$;

$$ = $.fn.tinymce = function() {

    if (!tinyMCE) {
        throw "Please include 'tinymce/tiny_mce.js' before use this plugin.";
    }

    return this.each(function() {
        var preString = "<div class='jqHTML_frame' style='width:" + $(this).css("width") + "px;height:" + ($(this).css("height") + 20) + "px;'><div>";
        var postString = "</div></div>";
        $(this).wrap(preString + postString);
        tinyMCE.execCommand('mceAddControl', false, this.id);
        jQuery.tinymce.controls.push(this.id);
    });
}

})(jQuery);