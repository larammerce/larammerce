define('address_service', ['jquery', 'fastselect'], function (jQuery) {
    var addressModuleSelector = "#address-module";
    var addressModuleElement = jQuery(addressModuleSelector);
    if (addressModuleElement.length !== 0) {
        var stateSelector = addressModuleElement.find('#state-selector');
        var citySelector = addressModuleElement.find('#city-selector');

        if (stateSelector.data('url')) {
            stateSelector.attr('type', 'hidden');
            stateSelector.fastselect({
                onItemSelect: function ($item, itemModel) {
                    stateSelector.val(itemModel.value);

                    citySelector.data('url', citySelector.data('url-base') + '?state_id=' + itemModel.value);
                    citySelector.attr('type', 'hidden');
                    citySelector.fastselect({
                        onItemSelect: function ($item, itemModel) {
                            citySelector.val(itemModel.value);
                        }
                    });
                }
            });
        }

        if (citySelector.data('url')) {
            citySelector.attr('type', 'hidden');
            citySelector.fastselect({
                onItemSelect: function ($item, itemModel) {
                    citySelector.val(itemModel.value);
                }
            });
        } else if (stateSelector.val()) {
            citySelector.data('url', citySelector.data('url-base') + '?state_id=' + stateSelector.val());
            citySelector.attr('type', 'hidden');
            citySelector.fastselect({
                onItemSelect: function ($item, itemModel) {
                    citySelector.val(itemModel.value);
                }
            });
        }

    }
});