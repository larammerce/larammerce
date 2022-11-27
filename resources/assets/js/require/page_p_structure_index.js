if (window.PAGE_ID === "admin.pages.p-structure.index") {
    require(["jquery", "bootstrap"], function (jQuery) {
        const uploadExcelModal = jQuery("#upload-p-structure-excel-file");
        const mainForm = uploadExcelModal.find("form");

        jQuery(".p-structure-upload-excel").on("click", function (uploadButtonClickedEvent) {
            uploadButtonClickedEvent.stopPropagation();
            uploadButtonClickedEvent.preventDefault();

            const uploadButton = jQuery(this);
            const formAction = uploadButton.data("href");

            mainForm.attr("action", formAction);
            uploadExcelModal.modal("show");

            return false;
        });
    });
}