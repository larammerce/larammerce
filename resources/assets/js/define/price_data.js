define('price_data', ['jquery', 'tools'], function (jQuery, tools) {
    jQuery.fn.formatPrice = function () {
        this.each(function (index) {
            var thisEl = jQuery(this);
            var result = tools.dropNonDigits(thisEl.text());
            result = tools.convertNumberToPersian(result);
            result = tools.formatNumber(result);
            thisEl.text(result);
        });
    }
});