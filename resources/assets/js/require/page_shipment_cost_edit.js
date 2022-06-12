if (window.PAGE_ID === "admin.pages.shipment-cost.edit") {
    require(["jquery", "template", "price_control"], function (jQuery, template) {
        const rowsContainer = jQuery("#custom-config-container");
        const mainForm = rowsContainer.closest("form");
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
            state: null, state_id: null, shipment_cost: 0,
        }) {
            const newRow = jQuery(template.shipmentCostCustomStateRow({
                index: counter, ...rowData
            }));

            newRow.find("input[name$='[shipment_cost]']").priceControl();

            if (rowData.state_id === null) {
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

        _.each(rowsData, function (iterRow, state_id) {
            addNewRow({
                state_id, ...iterRow
            });
        });

        mainForm.submit(function (submitEvent) {
            mainForm.find("input").each(function () {
                const thisInput = jQuery(this);
                if (typeof thisInput.attr("name") !== "undefined" && thisInput.attr("name").indexOf("[state_id]") !== -1 && thisInput.val().length === 0) {
                    thisInput.remove();
                }
            });
        });

        addNewRow();

    });
}
