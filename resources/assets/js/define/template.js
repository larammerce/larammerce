define('template', ['jquery', 'underscore'], function (jQuery, _) {
    return {
        formInputTemplate: _.template(jQuery('#form-input').html()),
        formInputMessageTemplate: _.template(jQuery('#form-input-message').html()),
        tagsContainerTemplate: _.template(jQuery('#tags-container-template').html()),
        tagsElementTemplate: _.template(jQuery('#tag-element-template').html()),
        extraPropertyTemplate: _.template(jQuery('#extra-property-template').html()),
        virtualFormTemplate: _.template(jQuery('#virtual-form-template').html()),
        protectorLayer: _.template(jQuery('#protector-layer-template').html()),
        searchContainer: _.template(jQuery('#search-container').html()),
        searchResultItem: _.template(jQuery('#search-result-item').html()),
        queryScope: _.template(jQuery('#template-query-scope').html()),
        queryScopeSelect: _.template(jQuery('#template-query-scope-select').html()),
        queryScopeOption: _.template(jQuery('#template-query-scope-option').html()),
        queryScopeAndBtn: _.template(jQuery('#template-query-scope-and-btn').html()),
        queryScopeValue: _.template(jQuery('#template-query-scope-value').html()),
        searchableListSearchInput: _.template(jQuery('#searchable-list-search-input').html()),
        cmfRow: _.template(jQuery('#cmf-row').html()),
        surveyCustomStateRow: _.template(jQuery("#survey-custom-state-row").html()),
        shipmentCostCustomStateRow: _.template(jQuery("#shipment-cost-custom-state-row").html()),
        productPackageRow: _.template(jQuery("#product-package-row").html()),
        discountStepRow: _.template(jQuery("#discount-step-row").html()),
        modalButtonRow: _.template(jQuery("#modal-button-row").html()),
        representativeOptionRow: _.template(jQuery("#representative-option-row").html())
    }
});
