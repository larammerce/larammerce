if (window.PAGE_ID === "admin.pages.upgrade.index")
    require(["jquery"], function (jQuery) {

        let upgradeInProgress = false;
        const logLines = [];

        function toggleLoading() {
            if (upgradeInProgress) {
                jQuery("button, a, input").addClass("disabled").attr("disabled", "disabled").attr("loading", "loading");
                jQuery("#updating-note").fadeIn();
            } else {
                jQuery("[loading=loading]").removeClass("disabled").removeAttr("disabled").removeAttr("loading");
                jQuery("#updating-note").fadeOut();
            }
        }

        function handleUpgrade(url) {
            upgradeInProgress = true;
            toggleLoading();

            jQuery.ajax({
                url: url,
                type: 'GET',
                dataType: 'json'
            }).done(function (data) {
                let gettingLog = false;
                const upgradeInterval = setInterval(function () {
                    if (gettingLog) {
                        document.getElementById("output").innerHTML += " .";
                        return;
                    }

                    gettingLog = true;
                    jQuery.ajax({
                        url: '/admin/upgrade-log',
                        data: {
                            "line_number": logLines.length
                        },
                        type: 'GET',
                        dataType: 'json'
                    }).done(function (data) {
                        gettingLog = false;
                        if (data.log.length > 0) {
                            // split the data.log with \n
                            const lines = data.log.split("\n");
                            // remove the last line, because it's empty
                            lines.pop();

                            // add the lines to the logLines array
                            logLines.push(...lines);

                            // add the lines to the output div
                            document.getElementById("output").innerHTML += "<br>" + lines.join("<br>");

                            // see if the data.running is false stop the interval
                            if (!data.running) {
                                clearInterval(upgradeInterval);
                                upgradeInProgress = false;
                                toggleLoading();
                            }
                        }
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        gettingLog = false;
                        console.log()
                    });
                }, 2000);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                upgradeInProgress = false;
                toggleLoading();

                document.getElementById("output").innerHTML += "Another upgrade in progress! Try again in a minute!";
            });
        }

        window.onbeforeunload = function () {
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
