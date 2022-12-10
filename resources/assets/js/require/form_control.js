require(['jquery', 'form_control', 'toast'], function (jQuery) {
    jQuery('.form-control').formControl();
    jQuery(".error-messages ul").each(function () {
        const thisEl = jQuery(this);
        const inputName = thisEl.attr('input-name');
        const inputEl = jQuery('input[name="' + inputName + '"]');
        thisEl.find('li').each(function () {
            const messageEl = jQuery(this);
            if (inputEl.length > 0) {
                inputEl.trigger('message:danger', messageEl.text());
            } else {
                jQuery.toast({
                    text: messageEl.text(),
                    icon: messageEl.data('type'),
                    loader: true,
                    textAlign: 'right',
                    bgColor: messageEl.data('color'),
                    loaderBg: '#DE0426',
                    position: 'bottom-right',
                    hideAfter: 7000

                });
            }
        });

    });
});