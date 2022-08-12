if (window.PAGE_ID === "admin.pages.representative.edit") {
    require(["jquery", "template", "form_control"], function (jQuery, template) {
        const rowsContainer = jQuery("#options-container");
        const rowsData = rowsContainer.data("rows");
        const mainForm = rowsContainer.closest("form");
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
            title: null,
        }) {
            const newRow = jQuery(template.representativeOptionRow({
                index: counter,
                ...rowData
            }));

            if (rowData.title === null) {
                newRow.find(".actions-container .btn-danger").removeConfigRowButton().hide();
                newRow.find(".actions-container .btn-success").addConfigRowButton();
            } else {
                newRow.find(".actions-container .btn-danger").removeConfigRowButton();
                newRow.find(".actions-container .btn-success").addConfigRowButton().hide();
            }
            newRow.find("input").formControl();
            rowsContainer.append(newRow);

            counter += 1;
        }

        _.each(rowsData, function (iterRow, index) {
            addNewRow({
                title: iterRow
            });
        });

        addNewRow();

        mainForm.submit(function (submitEvent) {
            rowsContainer.find("div.row").last().remove();
        });

    });
}
