define('tag_control', ['jquery', 'underscore', 'tools', 'template', 'autocomplete', 'jquery_extra'], function (jQuery, _, tools, template) {
    (function (jQuery) {
        jQuery.fn.tagControl = function () {
            this.each(function (index) {
                var textArea = jQuery(this);
                textArea.suggestions = textArea.suggestions || [];
                textArea.name = (textArea.attr('name') || 'tags');
                textArea.openTag = textArea.data('open-tag') || '';
                textArea.openTagHref = function (id) {
                    return textArea.openTag.replace('-1', id);
                };
                textArea.value = _.map(textArea.closest('.tag-manager').find('ul[act="tag-data"] > li'),
                    function (dataElement) {
                        dataElement = jQuery(dataElement);
                        return {
                            id: dataElement.data('id'),
                            text: dataElement.data('text')
                        }
                    });
                textArea.attr('name', textArea.name + '-view');
                textArea.data('input-name', textArea.name);

                tools.getMainForm().append(template.formInputTemplate({inputName: textArea.name, inputValue: ''}));
                textArea.inputEl = jQuery('input[name="' + textArea.name + '"]');
                textArea.inputEl.addTag = function (tag, isInitial) {
                    if (_.findIndex(textArea.inputEl.tags, {text: tag.text}) === -1) {
                        if (isInitial !== true)
                            textArea.trigger('model:attach', tag);
                        textArea.inputEl.tags = textArea.inputEl.tags || [];
                        textArea.inputEl.tags.push(tag);
                        textArea.inputEl.val(JSON.stringify(textArea.inputEl.tags));
                        return true;
                    }
                    textArea.trigger('message:warning', "تگ " + tag.text + " در حال حاضر اضافه شده است.");
                    return false;
                };
                textArea.inputEl.popTag = function () {
                    textArea.inputEl.tags = textArea.inputEl.tags || [];
                    var popedTag = {};
                    if (textArea.inputEl.tags.length > 0) {
                        popedTag = textArea.inputEl.tags.pop();
                        textArea.trigger('model:detach', popedTag);
                    }
                    textArea.inputEl.val(JSON.stringify(textArea.inputEl.tags));
                    textArea.focus();
                    return popedTag;
                };
                textArea.inputEl.removeTag = function (tagId) {
                    var index = _.findIndex(textArea.inputEl.tags, {id: parseInt(tagId)});
                    if (textArea.inputEl.tags.length > 0 && index >= 0) {
                        textArea.trigger('model:detach', {id: tagId});
                        textArea.inputEl.tags.splice(index, 1);
                        textArea.inputEl.val(JSON.stringify(textArea.inputEl.tags));
                        return true;
                    }
                    return false;
                };

                textArea.parent().append(template.tagsContainerTemplate({name: textArea.name}));
                textArea.tagsContainerEl = jQuery('ul[tag-input-name="' + textArea.name + '"]');
                textArea.tagsContainerEl.parent = textArea.tagsContainerEl.parent();

                textArea.tagsContainerEl.reform = function () {
                    clearTimeout(window.reformTimeout);
                    window.reformTimeout = setTimeout(function () {
                        var tagsContainerParentWidth = textArea.tagsContainerEl.parent.width();
                        var tagsContainerParentLeft = textArea.tagsContainerEl.parent.offset().left;
                        var tagsContainerParentTop = textArea.tagsContainerEl.parent.offset().top;

                        var lastChild = textArea.tagsContainerEl.find('li:last-child');
                        if (lastChild.length) {
                            var lastChildTop = lastChild.offset().top;
                            var lastChildLeft = lastChild.offset().left;

                            var paddingTop = lastChildTop - tagsContainerParentTop + 8;
                            var paddingRight = tagsContainerParentWidth - (lastChildLeft - tagsContainerParentLeft) + 10;

                            textArea.css(
                                {
                                    'padding-right': paddingRight + 'px',
                                    'padding-top': paddingTop + 'px'
                                }
                            );
                        } else {
                            textArea.css(
                                {
                                    'padding-right': '10px',
                                    'padding-top': '28px'
                                }
                            );
                        }
                    }, 300);
                };
                textArea.tagsContainerEl.addTag = function (tag, isInitial) {
                    if (textArea.inputEl.addTag(tag, isInitial)) {
                        var newTagEl = jQuery(template.tagsElementTemplate(
                            {
                                id: tag.id,
                                text: tag.text,
                                inputName: textArea.name,
                                href: textArea.openTagHref(tag.id)
                            }));
                        newTagEl.anchorLink();
                        newTagEl.find('.remove-tag').on('click', function (event) {
                            event.preventDefault();
                            event.stopPropagation();
                            textArea.tagsContainerEl.removeTag(tag.id, this);
                            return false;
                        });
                        textArea.tagsContainerEl.append(newTagEl);
                        textArea.tagsContainerEl.reform();
                        return true;
                    }
                    return false;
                };
                textArea.tagsContainerEl.popTag = function () {
                    var tag = textArea.inputEl.popTag();
                    textArea.tagsContainerEl.find('li:last-child').remove();
                    textArea.tagsContainerEl.reform();
                    textArea.trigger('message:success', "تگ " + tag.text + " با موفقیت حذف شد.");
                };
                textArea.tagsContainerEl.removeTag = function (id, btnElement) {
                    btnElement = jQuery(btnElement);
                    var tagEl = btnElement.closest('li');

                    function removeElement() {
                        if (textArea.inputEl.removeTag(id)) {
                            tagEl.remove();
                            textArea.tagsContainerEl.reform();
                        }
                    }

                    window.customConfirm('آیا از پاک کردن تگ ' + tagEl.find('span.tag-text').text() + ' مطمئن هستید ؟',
                        removeElement, null);
                    return false;
                };

                _.each(textArea.value, function (item, elIndex) {
                    textArea.tagsContainerEl.addTag(item, true);
                });

                if (textArea.data('query')) {
                    textArea.autoComplete = textArea.autocomplete({
                        serviceUrl: textArea.data('query'),
                        transformResult: function (response) {
                            response = jQuery.parseJSON(response);
                            return {
                                suggestions: _.map(response.data.collection, function (dataItem) {
                                    return {value: dataItem.text, data: dataItem};
                                })
                            };
                        },
                        onSelect: function (selectedItem) {
                            if (textArea.tagsContainerEl.addTag(selectedItem.data)) {
                                textArea.val('');
                                textArea.focus();
                            }
                        },
                        onSearchStart: function (query) {
                            textArea.suggestions = [];
                        },
                        onSearchComplete: function (query, suggestions) {
                            textArea.suggestions = _.map(suggestions, function (dataItem) {
                                return {id: dataItem.data.id, text: dataItem.data.text};
                            });
                        },
                        appendTo: textArea.data('container'),
                        forceFixPosition: true
                    });
                }

                textArea.on('mousemove', function (event) {
                    if (!textArea.isMouseInElementContent(event)) {
                        textArea.tagsContainerEl.parent.addClass('move-top');
                    } else {
                        textArea.tagsContainerEl.parent.removeClass('move-top');
                    }
                });

                textArea.tagsContainerEl.parent.on('mousemove', function (event) {
                    if (!textArea.isMouseInElementContent(event)) {
                        textArea.tagsContainerEl.parent.addClass('move-top');
                    } else {
                        textArea.tagsContainerEl.parent.removeClass('move-top');
                    }
                });


                window.tagManagers = window.tagManagers || {};
                window.tagManagers[textArea.name] = textArea;
            })
                .on('keydown', function (event) {
                    var textArea = window.tagManagers[jQuery(this).data('input-name')];
                    var val = textArea.val();
                    if (event.key === 'Enter') {
                        event.preventDefault();

                        if (val.length > 0 && (textArea.suggestions.length === 0 || _.findIndex(textArea.suggestions,
                                {text: val}) === -1)) {

                            var formData = {};
                            formData[textArea.data('field-name')] = val;
                            formData._token = window.csrf_token;
                            //here must be ajax request to add the new tag to database
                            //in the success response of request, we must show success message
                            if (textArea.data('save')) {
                                jQuery.ajax({
                                    type: 'POST',
                                    url: textArea.data('save'),
                                    data: formData
                                }).success(function (response) {
                                    textArea.trigger('message:success', response.transmission.messages[0]);
                                    if (textArea.tagsContainerEl.addTag({
                                            id: response.data.item.id,
                                            text: response.data.item.text
                                        })) {
                                        textArea.val('');
                                    }
                                }).error(function (response) {
                                    response = response.responseJSON;
                                    textArea.trigger('message:warning', response.transmission.messages[0]);
                                });
                            } else {
                                textArea.trigger('message:success', 'تگ مورد نظر اضافه شد');
                                if (textArea.tagsContainerEl.addTag({
                                        id: 1,
                                        text: val
                                    })) {
                                    textArea.val('');
                                }
                            }
                        }

                        return false;
                    }

                    if (event.key === 'Backspace') {
                        if (textArea.val() === '' && textArea.inputEl.tags.length > 0) {
                            window.customConfirm('آیا از پاک کردن این تگ اطمینان دارید ؟',
                                textArea.tagsContainerEl.popTag, null);
                        }
                    }
                });
        };
    })(jQuery);
});