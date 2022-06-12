define('form_control', ['jquery', 'form_control_message'], function (jQuery) {
    (function (jQuery) {
        jQuery.fn.formControl = function () {
            this.on('focusin', function (event) {
                event.preventDefault();
                var inputGroup = jQuery(this).parent();
                inputGroup.addClass('focused');
            })
                .on('focusout', function (event) {
                    event.preventDefault();
                    var inputGroup = jQuery(this).parent();
                    inputGroup.removeClass('focused');
                })
                .on('change', function (event) {
                    event.preventDefault();
                    var input = jQuery(this);
                    if ((input.val() || input.data('value') || '').length > 0) {
                        input.parent().addClass('filled');
                    } else {
                        input.parent().removeClass('filled');
                    }
                })
                .on('message:danger', function (event, message) {
                    jQuery(this).showMessage(message, 'red');
                })
                .on('message:warning', function (event, message) {
                    jQuery(this).showMessage(message, 'orange');
                })
                .on('message:success', function (event, message) {
                    jQuery(this).showMessage(message, 'green');
                })
                .on('message:info', function (event, message) {
                    jQuery(this).showMessage(this, message, 'gray');
                })
                .each(function (index) {
                    var input = jQuery(this);
                    if ((input.val() || input.data('value') || '').length > 0) {
                        input.parent().addClass('filled');
                    }
                });
        };
    })(jQuery);
});