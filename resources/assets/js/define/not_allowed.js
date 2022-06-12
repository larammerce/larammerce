define('not_allowed', ["jquery", "template"], function (jQuery, template) {
    jQuery.fn.notAllowed = function (_position) {
        this.each(function (_index) {
            var thisEl = jQuery(this);
            _position = _position || 'relative';
            thisEl.css({
                position: _position
            });
            thisEl.append(template.protectorLayer({
                fadeLevel: 10,
                note: "دسترسی محدود شده"
            }));

        });
    };
});