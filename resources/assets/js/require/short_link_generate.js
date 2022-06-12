if (window.PAGE_ID === "admin.pages.short-link") {
    require(["jquery", "template"], function (jQuery) {
        jQuery(function () {
            const generate_btn = jQuery('#generate-short-link');
            const shortened_link_input = jQuery('#shortened-link-input');
            const shortened_link_input_div = jQuery('#shortened-link-input-div');
            generate_btn.on('click', function () {
                let string = generate_string(6);
                shortened_link_input_div.addClass('focused');
                shortened_link_input.val(string);
            });

            function generate_string(length) {
                var result           = '';
                var characters       = 'abcdefghijklmnopqrstuvwxyz';
                var charactersLength = characters.length;
                for ( var i = 0; i < length; i++ ) {
                    result += characters.charAt(Math.floor(Math.random() *
                        charactersLength));
                }
                return result;
            }
        });

    });
}