if (window.PAGE_ID === "admin.pages.upgrade.index")
    require(["jquery"], function (jQuery) {

        let upgradeInProgress = false;

        function handleUpgrade(url) {
            upgradeInProgress = true;
            const source = new EventSource(url);

            jQuery("button, a, input").addClass("disabled").attr("disabled", "disabled").attr("loading", "loading");
            jQuery("#updating-note").fadeIn();

            source.onmessage = function(event) {
                // Close the connection if the server sends a specific end signal.
                if (event.data.startsWith('END:')) {
                    source.close();
                    upgradeInProgress = false;
                    jQuery("#updating-note").fadeOut();
                    jQuery("[loading=loading]").removeClass("disabled").removeAttr("disabled").removeAttr("loading");
                    document.getElementById("output").innerHTML += "Process finished working.<br>";
                }else{
                    document.getElementById("output").innerHTML += event.data + "<br>";
                }
            };

            source.onerror = function(event) {
                source.close();
                console.error("EventSource failed:", event);
            };
        }

        window.onbeforeunload = function() {
            if (upgradeInProgress) {
                return "Upgrade in progress. Are you sure you want to leave this page?";
            }
        }

        jQuery('#upgradeThemeButton').click(function (event) {
            event.preventDefault();
            handleUpgrade('/admin/upgrade?only_theme=1');
            return false;
        });

        jQuery('#upgradeCoreButton').click(function () {
            event.preventDefault();
            handleUpgrade('/admin/upgrade?only_core=1');
            return false;
        });

        jQuery('#upgradeAllButton').click(function () {
            event.preventDefault();
            handleUpgrade('/admin/upgrade');
            return false;
        });
    });
