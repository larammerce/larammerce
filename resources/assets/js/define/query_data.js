define('query_data', ['jquery', 'underscore', 'template'], function (jQuery, _, template) {
    var QueryData = null;

    var scopeTypes = [
        {id: "none", title: "انتخاب کنید", has_value: false}
    ];

    var scopeFields = [
        {id: "none", title: "انتخاب کنید"}
    ];

    var moduleEl = null;
    var containerEl = null;

    return QueryData = {
        _scopeCounter: 0,
        init: function (_moduleEl) {
            moduleEl = _moduleEl;
            if (moduleEl != null && moduleEl.length !== 0)
                containerEl = moduleEl.find('.query-data-container');
            else
                return;

            moduleEl.find('ul.scope-types>li').each(function (_typeIndex) {
                var thisTypeEl = jQuery(this);
                var newType = {
                    id: thisTypeEl.data('id'),
                    title: thisTypeEl.data('title'),
                    has_value: thisTypeEl.data('has-value'),
                    options: [{id: "none", title: "انتخاب کنید", comment: "ابتدا گزینه مورد نظر را انتخاب کنید."}]
                };
                thisTypeEl.find('ul.options>li').each(function (_optionIndex) {
                    var thisOptionEl = jQuery(this);
                    newType.options.push({
                        id: thisOptionEl.data('id'),
                        title: thisOptionEl.data('title'),
                        comment: thisOptionEl.data('comment')
                    });
                });
                scopeTypes.push(newType);
            });

            moduleEl.find('ul.scope-fields>li').each(function (_filedIndex) {
                var thisFieldEl = jQuery(this);
                scopeFields.push({
                    id: thisFieldEl.data('id'),
                    title: thisFieldEl.data('title')
                });
            });


            moduleEl.find('ul.initial-data>li').each(function (_initialDataIndex) {
                var thisInitialData = jQuery(this);
                QueryData.addScope({
                    type: thisInitialData.data('type'),
                    field: thisInitialData.data('field'),
                    option: thisInitialData.data('option'),
                    value: thisInitialData.data('value')
                });
            });

            QueryData.addScope();
            var andBtnEl = jQuery(template.queryScopeAndBtn({}));
            andBtnEl.on('click', function (_event) {
                _event.preventDefault();
                _event.stopPropagation();

                QueryData.addScope();

                return false;
            });
            moduleEl.append(andBtnEl);
        },
        addScope: function (_scope) {
            if (typeof _scope === "undefined")
                _scope = {
                    type: "none",
                    field: "none",
                    option: "none",
                    value: null
                };
            _scope.id = QueryData._scopeCounter++;
            QueryData.renderScopeEl(_scope);
        },
        renderScopeEl: function (_scope) {

            var scopeEl = jQuery(".scope[data-scope-id='" + _scope.id + "']");
            if (scopeEl.length === 0) {
                scopeEl = jQuery(template.queryScope({scope: _scope}));
                containerEl.append(scopeEl);
            }

            var scopeSelectTypeContainer = jQuery(template.queryScopeSelect({
                row_id: _scope.id,
                size: 3,
                select: {
                    id: "type",
                    title: "نوع"
                }
            }));
            scopeEl.html(scopeSelectTypeContainer);

            var scopeSelectType = scopeSelectTypeContainer.find('select');
            var scopeTypeOptions = _.map(scopeTypes, function (_type, _index) {
                return jQuery(template.queryScopeOption({
                    option: _type,
                    selected: _scope.type === _type.id
                }));
            });
            scopeSelectType.append(scopeTypeOptions);
            scopeSelectType.on('change', function (_event) {
                _event.preventDefault();

                _scope.type = scopeSelectType.val();
                _scope.option = "none";
                _scope.value = null;
                QueryData.renderScopeEl(_scope);

                return false;
            });

            var scopeSelectFieldContainer = jQuery(template.queryScopeSelect({
                row_id: _scope.id,
                size: 5,
                select: {
                    id: "field",
                    title: "فیلد"
                }
            }));
            scopeEl.append(scopeSelectFieldContainer);

            var scopeSelectField = scopeSelectFieldContainer.find('select');
            var scopeFieldOptions = _.map(scopeFields, function (_field, _index) {
                return jQuery(template.queryScopeOption({
                    option: _field,
                    selected: _scope.field === _field.id
                }))
            });
            scopeSelectField.append(scopeFieldOptions);
            scopeSelectField.on('change', function (_event) {
                _event.preventDefault();

                _scope.field = scopeSelectField.val();
                QueryData.renderScopeEl(_scope);

                return false;
            });

            var scopeSelectOptionContainer = jQuery(template.queryScopeSelect({
                row_id: _scope.id,
                size: 4,
                select: {
                    id: "option",
                    title: "گزینه"
                }
            }));
            scopeEl.append(scopeSelectOptionContainer);

            var selectedTypes = _.filter(scopeTypes, function (_type, _index) {
                return _type.id === _scope.type;
            });
            if (selectedTypes.length > 0) {
                var selectedType = selectedTypes[0];
                var options = selectedType.options || [];
                var scopeSelectOption = scopeSelectOptionContainer.find('select');
                var scopeOptionOptions = _.map(options, function (_option, _index) {
                    return jQuery(template.queryScopeOption({
                        option: _option,
                        selected: _scope.option === _option.id
                    }));
                });
                scopeSelectOption.append(scopeOptionOptions);
                scopeSelectOption.on('change', function (_event) {
                    _event.preventDefault();

                    _scope.option = scopeSelectOption.val();
                    QueryData.renderScopeEl(_scope);

                    return false;
                });

                if (selectedType.has_value) {
                    var selectedOptions = _.filter(selectedType.options, function (_option, _index) {
                        return _option.id === _scope.option;
                    });
                    if (selectedOptions.length > 0) {
                        var selectedOption = selectedOptions[0];
                        var scopeValueContainer = jQuery(template.queryScopeValue({
                            row_id: _scope.id,
                            value: _scope.value,
                            comment: selectedOption.comment
                        }));
                        scopeEl.append(scopeValueContainer);

                        var scopeValueTextarea = scopeValueContainer.find('textarea');
                        scopeValueTextarea.on('keyup keydown', function (_event) {
                            _scope.value = scopeValueTextarea.val();
                        });
                    }
                }
            }
        }
    };
});