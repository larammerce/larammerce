if (window.PAGE_ID === "admin.pages.env-file.edit")
    require(["jquery", "underscore", "template"], function (jQuery, _, template) {
        // Handle the Add row button click event
        jQuery('#add-row').on('click', function (event) {
            event.preventDefault();
            // Create a new row using the template
            const newRow = jQuery(template.envRowTemplate({key: '', value: '', counter: window.envRowCounter++}));

            newRow.find(".delete-row").on('click', function () {
                // Remove the row
                jQuery(this).closest('div.row').remove();
            });

            newRow.find('.form-control').formControl();

            // Append the new row to the list
            jQuery('#env-list').append(newRow);

            return false;
        });

        // Handle the Delete row button click event
        jQuery('#env-list .delete-row').on('click', function () {
            // Remove the row
            jQuery(this).closest('div.row').remove();
        });
    });