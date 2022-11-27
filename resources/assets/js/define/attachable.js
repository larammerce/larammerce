define('attachable', ['jquery', 'form_control', 'select2'], function (jQuery) {
    const directoryListElement = jQuery('#directory-list');

    directoryListElement.select2({
        'placeholder': 'دسته بندی'
    });
    (function (jQuery) {
        jQuery.fn.attachable = function () {
            this.formControl();
            this.on('model:attach', function (event, data) {
                var thisEl = jQuery(this);
                jQuery.ajax({
                    url: thisEl.data('attach'),
                    method: 'POST',
                    data: {
                        id: data.id,
                        _token: window.csrf_token
                    }
                }).done(function (result) {
                    try {
                        thisEl.trigger('message:success', result.transmission.messages[0]);
                    } catch (e) {
                        console.log(e);
                        console.log(result);
                    }
                });
            }).on('model:detach', function (event, data) {
                var thisEl = jQuery(this);

                jQuery.ajax({
                    url: thisEl.data('detach'),
                    method: 'POST',
                    data: {
                        id: data.id,
                        _token: window.csrf_token
                    }
                }).done(function (result) {
                    try {
                        thisEl.trigger('message:warning', result.transmission.messages[0]);
                    } catch (e) {
                        console.log(e);
                        console.log(result);
                    }
                });
            });
        };
    })(jQuery);

    window.onload = function () {
        localStorage.removeItem('attr-product');
    };
});
