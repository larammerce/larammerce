define('extra_properties', ['jquery', 'template', "form_control"], function (jQuery, template) {
    (function (jQuery) {
        jQuery.fn.extraProperties = function () {
            this.each(function (index) {
                var thisContainer = jQuery(this);
                thisContainer.latestRowId = 0;

                thisContainer.addNewProperty = function (title, value, type) {
                    var newProperty = jQuery(template.extraPropertyTemplate(
                        {
                            rowId: thisContainer.latestRowId,
                            title : title,
                            value : value,
                            type : type,
                        }
                    ));
                    thisContainer.latestRowId++;
                    newProperty.find('.add-btn').on('click', function () {
                        thisContainer.addNewProperty();
                    });

                    newProperty.find('.remove-btn').on('click', function () {
                        newProperty.remove();
                    });

                    newProperty.find(".form-control").formControl();

                    thisContainer.append(newProperty);
                };

                thisContainer.find('ul[act="extra-properties-data"] > li').each(function () {
                    var initialDataRow = jQuery(this);
                    thisContainer.addNewProperty(
                        initialDataRow.data('key'),
                        initialDataRow.data('value'),
                        initialDataRow.data('type'),
                    );
                });

                thisContainer.addNewProperty();
            });
        };
    })(jQuery);
});
