require(["jquery", "jqueryUi"], function (jQuery) {
    jQuery(function () {
        jQuery("#sortable").sortable({
            revert: true,
            start: function (event, ui) {

            },
            stop: function (event, ui) {
                jQuery('.extra-properties .ui-state-default').each(function (i) {
                    console.log(this);
                    $(this).find('input[type=hidden]').val(i);
                });
            }
        });
        jQuery("ul, li").disableSelection();

    });
});