/**
 * Checkboxes toggle
 *
 * Example:
 * <code>
 * </code>
 * @author Jace Ju
 * @license MIT License
 */
;(function($) { // plugin body start

var $$;

$$ = $.fn.waggleCheckboxes = function(settings) {

    $$.container = this;
    $$.settings = jQuery.extend($$.settings, settings);

    if (!$.fn.checkCheckboxes) {
        throw "Please include 'jquery/checkboxes.js' before use this plugin.";
    }

    return $$.container.each(function () {
        $(this).click($$.toggleCheckbox);
    });
};

$$.container = null;

$$.settings = {
    containerId: null
};

$$.toggleCheckbox = function () {
    if (typeof $$.settings.containerId == 'string') {
        $form = $($$.settings.containerId);
    } else {
        $form = $(this).parents('form:first');
    }
    if ($(this).attr('checked')) {
        $form.checkCheckboxes(':not(this)');
    } else {
        $form.unCheckCheckboxes(':not(this)');
    }
};

})(jQuery);

/**
 * Checkboxes submit
 */
;(function($) { // plugin body start

var $$;

$$ = $.fn.wamitCheckboxes = function(settings) {

    $$.container = this;
    $$.settings = jQuery.extend($$.settings, settings);

    return $$.container.each(function () {
        $(this).click($$.submitCheckboxIds);
    });
};

$$.container = null;

$$.settings = {
    checkboxClass: 'checkId',
    checkboxSubmitMessage: 'Please select the records for submit.',
    checkboxComfirmMessage: null
};

$$.submitCheckboxIds = function () {
    var checkIds = $('input.' + $$.settings.checkboxClass + '[@checked]');
    if (0 == checkIds.size()) {
        alert($$.settings.checkboxSubmitMessage);
        return false;
    }
    if ($$.settings.checkboxComfirmMessage) {
        msg = String($$.settings.checkboxComfirmMessage).replace(/%size%/, String(checkIds.size()));
        if (!confirm(msg)) {
            return false;
        }
    }
    return true;
};

})(jQuery);
