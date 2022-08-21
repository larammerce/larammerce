if (window.PAGE_ID === "admin.pages.discount-group.edit") {
    require(["jquery", "template", "price_control", "form_control"], function (jQuery, template) {
        const rowsContainer = jQuery("#discount-steps-container");
        const rowsData = rowsContainer.data("rows");
        let counter = 0;

        jQuery.fn.removeConfigRowButton = function () {
            const thisEl = jQuery(this);
            thisEl.on("click", function (event) {
                event.preventDefault();
                event.stopPropagation();
                thisEl.closest(".row").find("input").each(function () {
                    const name = this.name;
                    const inputName = name.replace(/-view/gi, "");
                    jQuery(`input[name="${inputName}"]`).remove();
                });
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
            amount: 0,
            value: 0
        }) {
            const newRow = jQuery(template.discountStepRow({
                index: counter,
                ...rowData
            }));

            newRow.find("input").priceControl();

            if (rowData.amount === 0) {
                newRow.find(".actions-container .btn-danger").removeConfigRowButton().hide();
                newRow.find(".actions-container .btn-success").addConfigRowButton();
            } else {
                newRow.find(".actions-container .btn-danger").removeConfigRowButton();
                newRow.find(".actions-container .btn-success").addConfigRowButton().hide();
            }
            newRow.find(".fast-select").fastselect();
            newRow.find(".form-control").formControl();
            rowsContainer.append(newRow);

            counter += 1;
        }

        _.each(rowsData, function (iterRow, state_id) {
            addNewRow({
                state_id,
                ...iterRow
            });
        });

        addNewRow();

    });
}
