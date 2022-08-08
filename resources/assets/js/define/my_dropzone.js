define('my_dropzone', ['jquery', 'template', 'dropzone', 'bootstrap'], function (jQuery, template) {
    var captionModal = jQuery('#add-caption-to-file-modal');
    captionModal.inputEl = captionModal.find('#file-caption-text');
    captionModal.inputLinkEl = captionModal.find('#file-link-text');
    captionModal.inputPriorityEl = captionModal.find('#file-priority-text');
    captionModal.form = captionModal.find('form');

    function dzAddCaption(file) {
        var el = jQuery(file.previewElement);
        var dzParent = el.closest('div.my-dropzone');
        var fileId = el.attr('file-id');
        var dzId = dzParent.attr('dz-id');
        var fileCaptionInput = jQuery('input[name="' + createFileCaptionInputName(dzId, fileId) + '"]');
        var fileLinkInput = jQuery('input[name="' + createFileLinkInputName(dzId, fileId) + '"]');
        var filePriorityInput = jQuery('input[name="' + createFilePriorityInputName(dzId, fileId) + '"]');
        var captionStoreAction = dzParent.attr('dz-caption') || '#';
        captionStoreAction = captionStoreAction.replace('-1', fileId);

        captionModal.inputEl.val(fileCaptionInput.val());
        captionModal.inputLinkEl.val(fileLinkInput.val());
        captionModal.inputPriorityEl.val(filePriorityInput.val());
        captionModal.modal('show');

        captionModal.form.unbind();
        captionModal.form.submit(function (event) {
            event.preventDefault();
            captionModal.modal('hide');

            fileCaptionInput.val(captionModal.inputEl.val());
            fileLinkInput.val(captionModal.inputLinkEl.val());

            jQuery.ajax({
                url: captionStoreAction,
                method: 'POST',
                data: {
                    _method: 'PUT',
                    _token: window.csrf_token,
                    caption: captionModal.inputEl.val(),
                    link: captionModal.inputLinkEl.val(),
                    priority: captionModal.inputPriorityEl.val()
                }
            }).success(function (result) {
                dzParent.trigger('message:success', result.transmission.messages[0]);
            }).error(function (result) {
                console.log(result);
            });

            return false;
        });
    }

    function dzSetMainFile(file) {
        var el = jQuery(file.previewElement);
        var dzParent = el.closest('div.my-dropzone');
        var fileId = el.attr('file-id');
        var setMainAction = dzParent.attr('dz-main') || '#';
        setMainAction = setMainAction.replace('-1', fileId);
        dzParent.find('.dz-preview.selected-as-main').removeClass('selected-as-main');
        el.addClass('selected-as-main');

        jQuery.ajax({
            url: setMainAction,
            method: 'POST',
            data: {
                _method: 'PUT',
                _token: window.csrf_token
            }
        }).success(function (result) {
            dzParent.trigger('message:success', result.transmission.messages[0]);
        }).error(function (result) {
            console.log(result);
        });
    }

    function dsSetSecondaryFile(file) {
        var el = jQuery(file.previewElement);
        var dzParent = el.closest('div.my-dropzone');
        var fileId = el.attr('file-id');
        var setSecondaryAction = dzParent.attr('dz-secondary') || '#';
        setSecondaryAction = setSecondaryAction.replace('-1', fileId);
        dzParent.find('.dz-preview.selected-as-secondary').removeClass('selected-as-secondary');
        el.addClass('selected-as-secondary');

        jQuery.ajax({
            url: setSecondaryAction,
            method: 'POST',
            data: {
                _method: 'PUT',
                _token: window.csrf_token
            }
        }).success(function (result) {
            dzParent.trigger('message:success', result.transmission.messages[0]);
        }).error(function (result) {
            console.log(result);
        });
    }

    function dzRemove(file, dzParent) {
        var el = jQuery(file.previewElement);
        var fileId = el.attr('file-id');
        var removeAction = dzParent.attr('dz-remove') || '#';
        removeAction = removeAction.replace('-1', fileId);
        jQuery.ajax({
            url: removeAction,
            method: 'POST',
            data: {
                _method: 'DELETE',
                _token: window.csrf_token
            }
        }).success(function (result) {
            dzParent.trigger('message:success', result.transmission.messages[0]);
        }).error(function (result) {
            console.log(result);
        });
    }

    function createFileInputName(dzId, fileId) {
        return dzId + '__file__' + fileId;
    }

    function createFileCaptionInputName(dzId, fileId) {
        return dzId + '__file-caption__' + fileId;
    }

    function createFileLinkInputName(dzId, fileId) {
        return dzId + '__file-link__' + fileId;
    }

    function createFilePriorityInputName(dzId, fileId) {
        return dzId + '__file-priority__' + fileId;
    }

    Dropzone.setMainFile = dzSetMainFile;
    Dropzone.setSecondaryFile = dsSetSecondaryFile;
    Dropzone.addCaptionToFile = dzAddCaption;

    jQuery.fn.myDropzone = function () {
        this.each(function () {
            var thisEl = jQuery(this);
            var thisUrl = thisEl.attr('dz-action') || '#';
            var thisId = thisEl.attr('dz-id') || 'none';
            window.dzElements = window.dzElements || {};
            window.dzElements[thisId] = new Dropzone('*[dz-id="' + thisId + '"]', {
                url: thisUrl,
                params: {_token: window.csrf_token}
            });
            window.dzElements[thisId].on('removedfile', function (file) {
                var fileId = jQuery(file.previewElement).attr('file-id');
                jQuery('input[name="' + createFileInputName(thisId, fileId) + '"]').remove();
                jQuery('input[name="' + createFileCaptionInputName(thisId, fileId) + '"]').remove();
                jQuery('input[name="' + createFileLinkInputName(thisId, fileId) + '"]').remove();
                jQuery('input[name="' + createFilePriorityInputName(thisId, fileId) + '"]').remove();
                dzRemove(file, thisEl);
            });
            window.dzElements[thisId].on('success', function (file, serverResponse) {
                var fileId = null;

                if (serverResponse.transmission) {
                    thisEl.trigger('message:success', serverResponse.transmission.messages[0]);
                }

                try {
                    fileId = serverResponse.data.model.id;
                    var fileEl = jQuery(file.previewElement);
                    fileEl.attr('file-id', fileId);
                    fileEl.find('*').attr('file-id', fileId);
                    thisEl.append(template.formInputTemplate({
                        inputName: createFileInputName(thisId, fileId),
                        inputValue: fileId
                    }));
                    thisEl.append(template.formInputTemplate({
                        inputName: createFileCaptionInputName(thisId, fileId),
                        inputValue: (_.has(serverResponse, 'mockFile') ? serverResponse.mockFile.caption : '')
                    }));
                    thisEl.append(template.formInputTemplate({
                        inputName: createFileLinkInputName(thisId, fileId),
                        inputValue: (_.has(serverResponse, 'mockFile') ? serverResponse.mockFile.link : '')
                    }));
                    thisEl.append(template.formInputTemplate({
                        inputName: createFilePriorityInputName(thisId, fileId),
                        inputValue: (_.has(serverResponse, 'mockFile') ? serverResponse.mockFile.priority : '')
                    }));

                    if (_.has(serverResponse, 'mockFile') && serverResponse.mockFile.isMain) {
                        fileEl.addClass('selected-as-main');
                    }

                    if (_.has(serverResponse, 'mockFile') && serverResponse.mockFile.isSecondary) {
                        fileEl.addClass('selected-as-secondary');
                    }
                } catch (exception) {
                    console.log(exception);
                    console.log('there is some error in loading in image with response file : ' +
                        serverResponse + '\n' + file);
                }
            });

            thisEl.find('ul.existing-files li').each(function (index) {
                var existingFileEl = jQuery(this);
                var response = {
                    data: {
                        model: {
                            id: existingFileEl.attr('file-id')
                        }
                    },
                    mockFile: {
                        id: existingFileEl.attr('file-id'),
                        name: existingFileEl.attr('file-name'),
                        size: existingFileEl.attr('file-size'),
                        url: existingFileEl.attr('file-url'),
                        caption: existingFileEl.attr('file-caption'),
                        link: existingFileEl.attr('file-link'),
                        priority: existingFileEl.attr('file-priority'),
                        isMain: (existingFileEl.attr('is-main') !== undefined),
                        isSecondary: (existingFileEl.attr('is-secondary') !== undefined)
                    }
                };
                window.dzElements[thisId].files.push(response.mockFile);
                window.dzElements[thisId].emit("addedfile", response.mockFile);
                window.dzElements[thisId].emit("thumbnail", response.mockFile, response.mockFile.url);
                window.dzElements[thisId].emit("complete", response.mockFile);
                window.dzElements[thisId].emit("success", response.mockFile, response);
            });
        });
    };
});
