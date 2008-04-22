/**
 * Form handler
 *
 * Example:
 * <code>
 * </code>
 * @author Jace Ju
 * @license MIT License
 */
;(function($) { // plugin body start

var $$;

$$ = $.fn.walidate = function(settings) {

    $$.container = this;

    $$.settings = jQuery.extend($$.settings, settings);

    if (!$.fn.ajaxForm) {
        throw "Please include 'jquery/form.js' before use this plugin.";
    }

    return $$.container.each(function() {
        var $domElement = $(this);
        if (!$domElement.is('form')) return;
        var options = {
            dataType: 'json',
            beforeSubmit: $$.beforeSubmit,
            success: $$.showMessages
        };
        if (typeof tinyMCE == 'undefined') {
            $domElement.ajaxForm(options);
        } else {
            $domElement.submit(function () {
                tinyMCE.formSubmit(this, false);
                $(this).ajaxSubmit(options);
                return false;
            });
        }
    });

};

$$.container = null;

$$.submitButtons = null;

$$.originalSubmitValue = null;

$$.settings = {
    submitMessage: 'Please wait...',
    successMessage: 'Done!',
    labelClass: 'error',
    hasError: function () {},
    beforeSubmit: function () { return true; },
    redirectUrl: null
};

$$.beforeSubmit = function (formData, jqForm, options) {
    if (false == !!$$.settings.beforeSubmit(formData, jqForm, options)) {
        return false;
    }
    $$.submitButtons = $(':submit', $$.container);
    $$.submitButtons.attr('disabled', true);
    if ($.blockUI) {
        $.extend($.blockUI.defaults.pageMessageCSS, { fontSize: '9pt', padding: '10px 0' });
        setTimeout(function () { $.blockUI($$.settings.submitMessage) }, 0);
    } else {
        $$.originalSubmitValue = $($$.submitButtons[0]).val();
        $($$.submitButtons[0]).val($$.settings.submitMessage);
    }
    return true;
};

$$.showMessages = function (json) {
    var labels = $('label.' + $$.settings.labelClass);
    labels.text('').hide();
    $($$.submitButtons[0]).val($$.originalSubmitValue);
    $(':submit', $$.container).attr('disabled', false);
    if ($.unblockUI) {  $.unblockUI(); }
    if (json.error) {
        $$.settings.hasError(json.messages);
        for (field in json.messages) {
            $('label#' + field + 'Label').text(json.messages[field][0]);
        }
        // show labels
        if ($.ec) {
            labels.show('pulsate', { method: 'pulsate', times: 3, duration: 100 });
        } else {
            labels.fadeIn().fadeOut().fadeIn().fadeOut().fadeIn();
        }
    } else {
        if ($$.settings.successMessage) {
            alert($$.settings.successMessage);
        }
        if ($$.settings.redirectUrl) {
            window.location.replace($$.settings.redirectUrl);
        }
    }
};

})(jQuery);
