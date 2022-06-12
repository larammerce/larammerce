if (window.PAGE_ID === "admin.pages.product-package.edit") {
    require(["jquery", "template"], function (jQuery, template) {
        const rowsContainer = jQuery("#product-package-container");
        const rowsData = rowsContainer.data("rows");
        let counter = 0;

        jQuery.fn.removeConfigRowButton = function () {
            const thisEl = jQuery(this);
            thisEl.on("click", function (event) {
                event.preventDefault();
                event.stopPropagation();
                thisEl.closest(".row").remove();
                return false;
            });
            return thisEl;
        };

        jQuery.fn.addConfigRowButton = function () {
            const thisEl = jQuery(this);
            thisEl.on("click", function (event) {
                event.preventDefault();
                event.stopPropagation();
                thisEl.siblings().show();
                thisEl.hide();
                addNewRow();
                return false;
            });
            return thisEl;
        }

        function addNewRow(rowData = {
            product_id: null,
            product_count: 1,
            product_title: null,
        }) {
            const newRow = jQuery(template.productPackageRow({
                index: counter,
                ...rowData
            }));

            if (rowData.product_id === null) {
                newRow.find(".actions-container .btn-danger").removeConfigRowButton().hide();
                newRow.find(".actions-container .btn-success").addConfigRowButton();
            } else {
                newRow.find(".actions-container .btn-danger").removeConfigRowButton();
                newRow.find(".actions-container .btn-success").addConfigRowButton().hide();
            }
            newRow.find(".fast-select").fastselect();
            rowsContainer.append(newRow);

            counter += 1;
        }

        _.each(rowsData, function (iterRow, product_id) {
            addNewRow({
                product_id,
                ...iterRow
            });
        });

        addNewRow();

    });
}