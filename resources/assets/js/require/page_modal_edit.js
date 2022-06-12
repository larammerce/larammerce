if (window.PAGE_ID === "admin.pages.modal") {
    require(["jquery", "template"], function (jQuery, template) {

        const rowsContainer = jQuery("#buttons-row-container");
        const rowsData = rowsContainer.data("rows");
        const buttonForm = rowsContainer.closest("form");

        let counter = 0;

        jQuery.fn.removeRowButton = function () {
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

        jQuery.fn.addRowButton = function () {
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
            text: null, tag_class: "btn btn-primary", type: "link", link: "#"
        }) {
            const newRow = jQuery(template.modalButtonRow({
                index: counter, ...rowData
            }));

            if (rowData.text === null) {
                newRow.find(".actions-container .btn-danger").removeRowButton().hide();
                newRow.find(".actions-container .btn-success").addRowButton();
            } else {
                newRow.find(".actions-container .btn-danger").removeRowButton();
                newRow.find(".actions-container .btn-success").addRowButton().hide();
            }
            newRow.find(".fast-select").fastselect();
            rowsContainer.append(newRow);

            counter += 1;
        }

        _.each(rowsData, function (iterRow, index) {
            addNewRow({
                index, ...iterRow
            });
        });

        buttonForm.submit(function () {
            buttonForm.find("input").each(function () {
                const thisInput = jQuery(this);
                if (thisInput.val().length === 0) {
                    thisInput.closest('.row').remove();
                }
            });
        });

        addNewRow();

        jQuery('.edit-route').on('click', function () {
            let route = jQuery(this).data('route');
            let action = window.location.origin + '/admin/modal-route/' + route['id'];

            jQuery('#edit-route-form').attr('action', action);
            jQuery('#edit-route').val(route['route']);
            if (route['children_included']) {
                jQuery('#edit-children-included').prop('checked', true);
            }
            jQuery('#edit-children-included_value').val(route['children_included']);
            if (route['self_included']) {
                jQuery('#edit-self-included').prop('checked', true);
            }
            jQuery('#edit-self-included_value').val(route['self_included']);
            jQuery('#edit-route-modal').modal('show');
        });

        jQuery('.check-route-includes').on('change', function () {
            if (this.checked) {
                jQuery('#' + this.id + '_value').val(1);
            } else {
                jQuery('#' + this.id + '_value').val(0);
            }
        });
    });
}

