if (window.PAGE_ID === "admin.pages.product-filter.edit") {
    require(["jquery", "template"], function (jQuery, template) {
        const selectedTagsContainer = jQuery(".filter-selected-tags-container");
        const filterInputs = jQuery("input.filter-select");

        const tagsContainer = jQuery(template.tagsContainerTemplate({name: "selected-tags-container"}));
        tagsContainer.addClass("non-abs");
        selectedTagsContainer.html(tagsContainer);

        const tagsInnerContainer = tagsContainer.find("ul");

        function createTag(thisFilterInput) {
            const selectedFilterTag = jQuery(template.tagsElementTemplate({
                id: thisFilterInput.attr("id"),
                href: "#",
                text: thisFilterInput.data("filter-select-title"),
                inputName: thisFilterInput.attr("name")
            }));

            selectedFilterTag.find("button").on("click", function (removeEvent) {
                removeEvent.preventDefault();
                thisFilterInput.prop("checked", false);
                selectedFilterTag.remove();
                return false;
            });

            tagsInnerContainer.append(selectedFilterTag);
        }

        filterInputs.on("change", function (event) {
            event.preventDefault();
            const thisFilterInput = jQuery(this);
            if (thisFilterInput.is(":checked")) {
                createTag(thisFilterInput);
            } else {
                tagsInnerContainer.find(".tag-element[tag-id='" + thisFilterInput.attr("id") + "']").remove();
            }
            return false;
        }).each(function (filterInputIndex) {
            const thisFilterInput = jQuery(this);
            if (thisFilterInput.is(":checked")) {
                createTag(thisFilterInput);
            }
        });
    });
}
