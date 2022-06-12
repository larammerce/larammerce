require(['jquery', 'form_control'], function (jQuery) {
    jQuery('.form-control').formControl();
    jQuery(".error-messages ul").each(function () {
        var thisEl = jQuery(this);
        var inputName = thisEl.attr('input-name');
        var inputEl = jQuery('input[name="' + inputName + '"]');
        if (inputEl) {
            thisEl.find('li').each(function () {
                inputEl.trigger('message:danger', jQuery(this).text());
            });
        }
    });
});