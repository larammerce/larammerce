define('jquery_extra', ['jquery', 'underscore', 'tools'], function (jQuery, _, tools) {
    (function (jQuery) {
        jQuery.fn.getPadding = function () {
            var element = jQuery(this);
            var padding = element.css('padding');
            var paddingParts = padding.split(' ');
            var values = _.map(paddingParts, function (padding) {
                return parseFloat(padding.split('px').join(''));
            });

            if (values.length === 4) {
                return {
                    top: values[0],
                    right: values[1],
                    bottom: values[2],
                    left: values[3]
                }
            } else if (values.length === 3) {
                return {
                    top: values[0],
                    right: values[1],
                    bottom: values[1],
                    left: values[2]
                }
            } else if (values.length === 2) {
                return {
                    top: values[0],
                    right: values[1],
                    bottom: values[1],
                    left: values[0]
                }
            } else if (values.length === 1) {
                return {
                    top: values[0],
                    right: values[0],
                    bottom: values[0],
                    left: values[0]
                }
            } else {
                return {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                }
            }
        };
        jQuery.fn.getElementContentOffset = function () {
            var element = jQuery(this);
            var padding = element.getPadding();
            var innerSize = {
                width: element.innerWidth(),
                height: element.innerHeight()
            };

            var offset = {
                top: element.offset().top,
                right: element.offset().left + innerSize.width,
                bottom: element.offset().top + innerSize.height,
                left: element.offset().left
            };

            return {
                top: offset.top + padding.top,
                right: offset.right - padding.right,
                bottom: offset.bottom - padding.bottom,
                left: offset.left + padding.left
            }
        };
        jQuery.fn.isMouseInElementContent = function (event) {
            var element = jQuery(this);
            return tools.isInOffset(element.getElementContentOffset(), {
                x: event.clientX,
                y: event.clientY
            });
        };
        jQuery.fn.anchorLink = function () {
            this.on('click', function (event) {
                event.preventDefault();
                tools.openLink(jQuery(this).attr('href'));
            });
        }
    })(jQuery);
});