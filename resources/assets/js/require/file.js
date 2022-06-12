require(["jquery", "file_service", "file_context_menu"], function (jQuery, FileService) {
    const fileContextMenu = jQuery('.file-context-menu').fileContextMenu();
    jQuery("[act='file']").fileController({
        contextMenu: fileContextMenu
    });

    jQuery('*[act="open-file"]').on('click', function (event) {
        FileService.openFile();
    });

    jQuery('*[act="edit-file"]').on('click', function (event) {
        FileService.editFile();
    });

    jQuery('*[act="show-file"]').on('click', function (event) {
        FileService.showFront();
    });

    jQuery('[act="cut-file"]').on('click', function (event) {
        const thisButton = jQuery(this);
        if (thisButton.hasClass('disabled'))
            return false;
        FileService.cutFiles();
    });

    jQuery('[act="copy-file"]').on('click', function (event) {
        const thisButton = jQuery(this);
        if (thisButton.hasClass('disabled'))
            return false;
        FileService.copyFiles();
    });

    jQuery('[act="paste-file"]').on('click', function (event) {
        const thisButton = jQuery(this);
        if (thisButton.hasClass('disabled'))
            return false;
        thisButton.addClass('disabled');
        FileService.pasteFiles();
    });
});