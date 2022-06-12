if (window.PAGE_ID === "admin.pages.sms-driver.edit") {
    require(['jquery', 'custom_radio_control'], function (jQuery, CustomRadioControl) {
        CustomRadioControl.init('input[name$="\[is_enabled\]"]');
    });
}
