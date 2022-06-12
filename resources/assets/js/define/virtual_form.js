define('virtual_form', ['jquery', 'underscore', 'template'], function (jQuery, _, template) {
    (function (jQuery) {
        jQuery.fn.virtualForm = function () {
            const body = jQuery('body');

            this.each(function (index) {
                const submitButton = jQuery(this);
                const actionAttr = submitButton.data('action') || submitButton.attr('href') || '#';
                const methodAttr = submitButton.data('method');
                const fields = submitButton.data('fields') || {};
                let method = methodAttr;

                switch (methodAttr) {
                    case 'DELETE' :
                        fields['_method'] = 'DELETE';
                        method = 'POST';
                        break;
                    case 'PUT' :
                        fields['_method'] = 'PUT';
                        method = 'POST';
                        break;
                }

                const virtualForm = jQuery(template.virtualFormTemplate({
                    formMethod: method,
                    formAction: actionAttr
                }));

                virtualForm.append(template.formInputTemplate({
                    inputName: '_token',
                    inputValue: window.csrf_token
                }));

                _.each(fields, function (value, key) {
                    virtualForm.append(template.formInputTemplate({
                        inputName: key,
                        inputValue: value
                    }));
                });

                body.append(virtualForm);

                submitButton.on('click', function (event) {
                    event.preventDefault();

                    if (submitButton.attr('confirm') !== undefined) {
                        window.currentForm = virtualForm;
                        window.customConfirm('آیا از انجام این عملیات اطمینان دارید ؟', window.submitForm);
                    } else {
                        virtualForm.submit();
                    }

                    return false;
                });

            });
        };
    })(jQuery);
});
