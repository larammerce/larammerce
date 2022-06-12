define('form_control_message', ['jquery', 'template'], function (jQuery, template) {
    (function (jQuery) {
        jQuery.fn.getMessageContainer = function () {
            var jEl = jQuery(this);
            var containerEl = jEl.parent();
            if (jEl.hasClass('my-dropzone')) {
                containerEl = jEl;
            }
            return containerEl;
        };

        jQuery.fn.showMessage = function (message, color) {
            window.formControlMessageTimeout = window.formControlMessageTimeout || {};
            var jEl = jQuery(this);
            var messageContainer = this.getMessageContainer();
            var messageEl = jQuery(template.formInputMessageTemplate({messageColor: color, message: message}));
            messageContainer.find('.message').remove();
            messageContainer.append(messageEl);
            messageEl.fadeIn();
            clearTimeout(window.formControlMessageTimeout[jEl.attr('name') || 'main']);
            window.formControlMessageTimeout[jEl.attr('name') || 'main'] = setTimeout(function () {
                messageEl.fadeOut();
                setTimeout(function () {
                    messageEl.remove();
                }, 1000);
            }, 10000);
        };
    })(jQuery);
});