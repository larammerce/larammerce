if (window.PAGE_ID === "admin.pages.survey.edit") {
    require(["jquery", "template"], function (jQuery, template) {
        const rowsContainer = jQuery("#custom-config-container");
        const rowsData = rowsContainer.data("rows");
        let counter = 0;

        jQuery.fn.removeSurveyConfigRowButton = function () {
            const thisEl = jQuery(this);
            thisEl.on("click", function (event) {
                event.preventDefault();
                event.stopPropagation();
                thisEl.closest(".row").remove();
                return false;
            });
            return thisEl;
        };

        jQuery.fn.addSurveyConfigRowButton = function () {
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
            state: null,
            state_id: null,
            custom_delay_hours: 0,
            custom_delay_days: 0,
            custom_survey_url: ''
        }) {
            const newRow = jQuery(template.surveyCustomStateRow({
                index: counter,
                ...rowData
            }));

            if (rowData.state_id === null) {
                newRow.find(".actions-container .btn-danger").removeSurveyConfigRowButton().hide();
                newRow.find(".actions-container .btn-success").addSurveyConfigRowButton();
            } else {
                newRow.find(".actions-container .btn-danger").removeSurveyConfigRowButton();
                newRow.find(".actions-container .btn-success").addSurveyConfigRowButton().hide();
            }
            newRow.find(".fast-select").fastselect();
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