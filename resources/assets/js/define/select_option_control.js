define('select_option_control', ['jquery'], function (jQuery) {
    (function (jQuery) {
        jQuery.fn.selectOptionControl = function () {
            var thisEl = jQuery(this);

            optionControl();
            this.on('change', optionControl);


            function optionControl() {
                thisEl.find('option').each(function () {
                    var option = jQuery(this);
                    var targetContainerSelector = option.data('target-container');
                    if (targetContainerSelector) {
                        var targetContainer = jQuery(targetContainerSelector);
                        if (targetContainer) {
                            if (option.is(':selected'))
                                targetContainer.show();
                            else
                                targetContainer.hide();
                        }
                    }
                })
            }
        };
    })(jQuery);
});