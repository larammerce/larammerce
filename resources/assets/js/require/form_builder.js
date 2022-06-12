if (window.current_page === "admin.pages.customer-meta-category.edit") {
    require(["jquery", "underscore", "template"], function (jQuery, _, template) {
        const rowsContainer = jQuery("#dynamic-form-rows-container");
        const form = rowsContainer.closest("form");
        const rowsData = rowsContainer.data("rows");
        let rowsCounter = 0;
        let latestNewRow;

        Object.values(rowsData).forEach((iterItem) => {
            addNewCmfRow(iterItem);
        })

        addNewCmfRow();

        function addNewCmfRow(data =
                                  {
                                      input_type: "",
                                      input_fill_by: "",
                                      input_title: "",
                                      input_identifier: "",
                                      input_content: "",
                                      is_empty: true
                                  }) {
            const newRow = jQuery(template.cmfRow({...data, index: rowsCounter}));
            const removeButton = newRow.find(".btn.btn-danger");
            removeButton.on("click", function (clickEvent) {
                newRow.remove();
            });

            const addButton = newRow.find(".btn.btn-success");
            addButton.on("click", function () {
                removeButton.css({display: "inline-block"})
                addButton.css({display: "none"});
                addNewCmfRow();
            });

            if ("is_empty" in data) {
                removeButton.css({display: "none"});
                latestNewRow = newRow;
            } else {
                addButton.css({display: "none"});
            }

            rowsContainer.append(newRow);
            rowsCounter += 1;
            return newRow;
        }

        form.on("submit", function (submitEvent) {
            latestNewRow.remove();
        });
    });
}