require(["jquery"], function (jQuery) {
    const navAppToggleButton = jQuery(".nav-toggle-btn button.toggle-btn");
    const body = jQuery("body");
    const upIcon = navAppToggleButton.find(".up-icon");
    const downIcon = navAppToggleButton.find(".down-icon");
    const navAppStorageKey = "NAV_APP_OPEN";
    window.navAppOpen = localStorage.getItem(navAppStorageKey);

    if (window.navAppOpen === null) {
        window.navAppOpen = true;
    } else {
        window.navAppOpen = JSON.parse(window.navAppOpen);
    }

    function toggleNavApp() {
        window.navAppOpen = !window.navAppOpen;
        localStorage.setItem(navAppStorageKey, window.navAppOpen);

        refreshIcon();
    }

    function refreshIcon() {
        if (window.navAppOpen) {
            upIcon.fadeIn(0);
            downIcon.fadeOut(0);
            body.addClass("nav-app-open");
            body.removeClass("nav-app-close");
        } else {
            upIcon.fadeOut(0);
            downIcon.fadeIn(0);
            body.removeClass("nav-app-open");
            body.addClass("nav-app-close");
        }
    }

    navAppToggleButton.on("click", function () {
        toggleNavApp();
    });

    refreshIcon();

});

require(["jquery"], function (jQuery) {
    const exploreTreeButton = jQuery(".tree-toggle-btn button.toggle-btn");
    const body = jQuery("body");
    const closeIcon = exploreTreeButton.find(".close-icon");
    const openIcon = exploreTreeButton.find(".open-icon");
    const exploreTreeStorageKey = "EXPLORE_TREE_OPEN";
    window.exploreTreeOpen = localStorage.getItem(exploreTreeStorageKey);

    if (window.exploreTreeOpen === null) {
        window.exploreTreeOpen = true;
    } else {
        window.exploreTreeOpen = JSON.parse(window.exploreTreeOpen);
    }

    function toggleExploreTree() {
        window.exploreTreeOpen = !window.exploreTreeOpen;
        localStorage.setItem(exploreTreeStorageKey, window.exploreTreeOpen);

        refreshIcon();
    }

    function refreshIcon() {
        if (window.exploreTreeOpen) {
            closeIcon.fadeIn(0);
            openIcon.fadeOut(0);
            body.addClass("explore-tree-open");
            body.removeClass("explore-tree-close");
        } else {
            closeIcon.fadeOut(0);
            openIcon.fadeIn(0);
            body.removeClass("explore-tree-open");
            body.addClass("explore-tree-close");
        }
    }

    exploreTreeButton.on("click", function () {
        toggleExploreTree();
    });

    refreshIcon();

});
