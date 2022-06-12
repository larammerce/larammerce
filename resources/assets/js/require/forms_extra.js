require(['jquery'], function (jQuery) {
    jQuery('form[form-with-hidden-checkboxes]').submit(function () {
        jQuery(this).find('input[type=checkbox]').each(function () {
            if (this.checked) {
                const id = this.id.replace(/[\[\]]/gi, function (part) {
                    return '\\' + part;
                });
                jQuery("#" + id + "_hidden").remove();
            }
        });
    });
});
