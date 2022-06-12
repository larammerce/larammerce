define("file_context_menu", ["jquery", "file_service"], function (jQuery, FileService) {
    jQuery.fn.fileContextMenu = function () {
        const thisJQObject = jQuery(this);
        thisJQObject.show = (xPos, yPos) => {
            const wScreen = jQuery(window).width(),
                hScreen = jQuery(window).height();

            const wContext = thisJQObject.width(),
                hContext = thisJQObject.height();

            if (xPos >= wContext) {
                xPos = xPos - wContext;
            } else if (wScreen - xPos < wContext) {
                xPos = 0;
            }

            if (hScreen - yPos < hContext) {
                yPos = yPos - hContext;
            }

            thisJQObject.css({
                left: xPos + "px",
                top: yPos + "px",
            });
            thisJQObject.fadeIn();
        };
        thisJQObject.hide = () => {
            thisJQObject.fadeOut();
        };
        jQuery("body").on("click", function (event) {
            thisJQObject.hide();
        });
        return thisJQObject;
    };
});