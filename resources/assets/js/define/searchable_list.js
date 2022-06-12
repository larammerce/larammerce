define("searchable_list", ["jquery", "template"], function (jQuery, template) {
    jQuery.fn.searchableList = function () {
        this.each(function (elementIndex) {
            const thisJQObject = jQuery(this);
            const options = thisJQObject.children('li');

            let timeout = null;

            const thisSearchInputContainer = jQuery(template.searchableListSearchInput({
                list_id: thisJQObject.data("searchable-id"),
                list_title: thisJQObject.data("searchable-title")
            }));
            const thisSearchInput = thisSearchInputContainer.find("input");
            const thisSearchForm = thisSearchInputContainer.find("form");
            const thisSearchSubmit = thisSearchInputContainer.find(".btn.submit");
            const thisSearchClear = thisSearchInputContainer.find(".btn.clear");

            thisSearchForm.on("submit", function (event) {
                event.preventDefault();
                return false;
            });

            thisSearchClear.on("click", function (event) {
                event.preventDefault();
                thisSearchInput.val("");
                thisSearchInput.trigger("change");
                return false;
            });


            thisSearchInputContainer.on("keyup change", function (event) {
                event.preventDefault();
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    if (thisSearchInput.val().length > 0) {
                        thisSearchSubmit.fadeOut(0);
                        thisSearchClear.fadeIn(0);
                    } else {
                        thisSearchSubmit.fadeIn(0);
                        thisSearchClear.fadeOut(0);
                    }
                    options.each(function (optionIndex) {
                        const thisOption = jQuery(this);

                        if (thisOption.data("searchable-title").trim().indexOf(thisSearchInput.val()) === -1) {
                            thisOption.fadeOut();
                        } else {
                            thisOption.fadeIn();
                        }
                    });
                }, 300);
                return false;
            });

            thisJQObject.prepend(thisSearchInputContainer);
        });
    };
});