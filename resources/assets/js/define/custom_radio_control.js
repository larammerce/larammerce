define("custom_radio_control", ["jquery"], function (jQuery) {
    return {
        init: function (selector) {
            const isCheckedControls = jQuery(selector)
            isCheckedControls.on("change", function (event) {
                if (this.checked) {
                    isCheckedControls.each(function () {
                        if (this !== event.target)
                            this.checked = false;
                    });
                }

            });
        }
    }
});
