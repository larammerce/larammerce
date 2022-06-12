define("file_service", ["jquery", "underscore"],
    function (jQuery, _) {
        let currentFile = null;
        let clicks = 0;
        let dblTimer = null;
        let hldTimer = null;
        const selectedFiles = {};

        jQuery.fn.fileController = function (config) {
            const {contextMenu} = config;

            this.each(function () {
                const thisJQObject = jQuery(this);

                thisJQObject.select = () => {
                    if (thisJQObject.hasClass("selected")) {
                        thisJQObject.removeClass("selected");
                        delete selectedFiles[thisJQObject.getId()];
                    } else {
                        thisJQObject.addClass("selected");
                        selectedFiles[thisJQObject.getId()] = thisJQObject;
                    }
                    FileService.checkButtons();
                };

                thisJQObject.open = () => {
                    if (thisJQObject.attr('target') && thisJQObject.attr('target') === '_blank') {
                        window.open(thisJQObject.attr('href'), '_blank');
                    } else {
                        location.href = thisJQObject.attr('href') || '#';
                    }
                };

                thisJQObject.edit = () => {
                    location.href = thisJQObject.attr('edit-href') || '#';
                };

                thisJQObject.showFront = () => {
                    location.href = thisJQObject.attr('show-href') || '#';
                };

                thisJQObject.getType = () => {
                    return thisJQObject.data("file-type");
                };

                thisJQObject.getId = () => {
                    return thisJQObject.data("file-id");
                };

                thisJQObject.on('dblclick', function (event) {
                    event.preventDefault();
                    return false;
                }).on('click', function (event) {
                    event.preventDefault();
                    clicks++;
                    if (clicks === 1) {
                        dblTimer = setTimeout(function () {
                            if (thisJQObject.hasClass('dir-tree')) {
                                thisJQObject.toggleClass('dir-open');
                                thisJQObject.find('[act="file"]').removeClass('dir-open');
                            } else
                                thisJQObject.select();
                            clicks = 0;
                        }, 300);
                    } else {
                        clearTimeout(dblTimer);
                        thisJQObject.open();
                        clicks = 0;
                    }
                    return false;
                }).on("mousedown", function (event) {
                    event.preventDefault();
                    hldTimer = setTimeout(function () {
                        currentFile = thisJQObject;
                        contextMenu.show(event.clientX, event.clientY);
                    }, 1000);
                    return false;
                }).on("mouseup", function (event) {
                    event.preventDefault();
                    clearTimeout(hldTimer);
                    return false;
                }).contextmenu(function (event) {
                    event.preventDefault();
                    currentFile = thisJQObject;
                    FileService.checkButtons();
                    contextMenu.show(event.clientX, event.clientY);
                    return false;
                });
            });
        };

        const FileService = {
            current: () => {
                return currentFile;
            },
            cutFiles: () => {
                if (FileService.getSelectedFiles().length > 0) {
                    const data = {
                        action: 'move',
                        ids: [],
                        _token: window.csrf_token
                    };

                    _.each(selectedFiles, function (thisFile, id) {
                        thisFile.addClass('cut-item');

                        if (!data.hasOwnProperty('type')) {
                            data['type'] = thisFile.getType();
                        }

                        data.ids.push(id);
                    });

                    jQuery.ajax({
                        url: '/admin/api/v1/clip-board/cut',
                        type: 'POST',
                        data: data
                    }).success(function (result) {
                        FileService.emptyClipboard();
                        alert('کات با موفقیت انجام شد، در پوشه مقصد دکمه بازنشانی را بزنید.');
                    }).error(function (error) {
                        console.log(error);
                    });
                }
            },
            copyFiles: () => {
                if (FileService.getSelectedFiles().length > 0) {
                    const data = {
                        action: 'copy',
                        ids: [],
                        _token: window.csrf_token
                    };

                    _.each(selectedFiles, function (thisFile, id) {
                        thisFile.addClass('copy-item');

                        if (!data.hasOwnProperty('type')) {
                            data['type'] = thisFile.getType();
                        }

                        data.ids.push(id);
                    });

                    jQuery.ajax({
                        url: '/admin/api/v1/clip-board/copy',
                        type: 'POST',
                        data: data
                    }).success(function (result) {
                        FileService.emptyClipboard();
                        alert('کپی با موفقیت انجام شد، در پوشه مقصد دکمه بازنشانی را بزنید.');
                    }).error(function (error) {
                        console.log(error);
                    });
                }
            },
            emptyClipboard: () => {
                _.each(selectedFiles, (thisFile, id) => {
                    thisFile.removeClass('selected').removeClass('cut-item');
                    delete selectedFiles[id];
                });
                FileService.checkButtons();
            },
            getSelectedFiles: () => {
                return _.values(selectedFiles);
            },
            checkButtons: () => {
                if (FileService.getSelectedFiles().length > 0) {
                    jQuery("[act='cut-file']").removeClass('disabled');
                    jQuery("[act='copy-file']").removeClass('disabled');
                } else {
                    jQuery("[act='cut-file']").addClass('disabled');
                    jQuery("[act='copy-file']").addClass('disabled');
                }
            },
            pasteFiles: () => {
                jQuery.ajax({
                    url: '/admin/api/v1/clip-board/paste',
                    type: 'POST',
                    data: {
                        _token: window.csrf_token,
                        directory_id: window.directory_id
                    }
                }).success(function (result) {
                    location.reload(true);
                }).error(function (error) {
                    console.log(error);
                });
            },
            openFile: () => {
                currentFile.open();
            },
            editFile: () => {
                currentFile.edit();
            },
            showFront: () => {
                currentFile.showFront();
            }
        };

        return FileService;
    });