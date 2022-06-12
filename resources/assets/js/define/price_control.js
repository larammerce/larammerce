define('price_control', ['jquery', 'tools', 'template'], function (jQuery, tools, template) {
    (function (jQuery) {
        jQuery.fn.priceControl = function () {
            this.on('keyup', function (event) {
                var thisEl = jQuery(this);
                var correctNumber = tools.dropNonDigits(thisEl.val());
                var englishNumber = tools.convertNumberToEnglish(correctNumber);
                var persianNumber = tools.convertNumberToPersian(correctNumber);
                window.priceInputs[thisEl.attr('name')].val(englishNumber);
                thisEl.val(tools.formatNumber(persianNumber));
            }).each(function (index) {
                window.priceInputs = window.priceInputs || {};

                var thisEl = jQuery(this);
                var name = (thisEl.attr('name') || 'price');
                thisEl.attr('name', name + '-view');
                tools.getMainForm().append(template.formInputTemplate({inputName: name, inputValue: 0}));
                window.priceInputs[thisEl.attr('name')] = jQuery('input[name="' + name + '"]');

                var correctNumber = tools.dropNonDigits(thisEl.val());
                var englishNumber = tools.convertNumberToEnglish(correctNumber);
                var persianNumber = tools.convertNumberToPersian(correctNumber);
                window.priceInputs[thisEl.attr('name')].val(englishNumber);
                thisEl.val(tools.formatNumber(persianNumber));
            });
        };
    })(jQuery);
});