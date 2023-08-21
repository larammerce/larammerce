if (window.PAGE_ID === "admin.pages.upgrade.index")
    require(["jquery"], function (jQuery) {

        let upgradeInProgress = false;
        let dotCounter = 0;
        let logLines = [];
        let counter = 0;

        function toggleLoading() {
            if (upgradeInProgress) {
                jQuery("button, a, input").addClass("disabled").attr("disabled", "disabled").attr("loading", "loading");
                jQuery("#updating-note").fadeIn();
            } else {
                jQuery("[loading=loading]").removeClass("disabled").removeAttr("disabled").removeAttr("loading");
                jQuery("#updating-note").fadeOut();
            }
        }

        function printDot(){
            if(dotCounter > 5) {
                dotCounter = 0;
                document.getElementById("output").innerHTML = "<br/>";
            }else{
                dotCounter += 1;
                document.getElementById("output").innerHTML += ".";
            }
        }

        function handleUpgrade(url) {
            upgradeInProgress = true;
            toggleLoading();
            logLines = [];
            counter = 0;

            jQuery.ajax({
                url: url,
                type: 'GET',
                dataType: 'json'
            }).done(function (data) {
                let gettingLogInProgress = false;
                const upgradeInterval = setInterval(function () {
                    if (gettingLogInProgress) {
                        printDot();
                        return;
                    }

                    if (counter > 10) {
                        clearInterval(upgradeInterval);
                        upgradeInProgress = false;
                        toggleLoading();
                        document.getElementById("output").innerHTML += "Upgrade timed out!";
                        dotCounter = 0;
                        return;
                    }

                    gettingLogInProgress = true;
                    jQuery.ajax({
                        url: '/admin/upgrade-log',
                        data: {
                            "line_number": logLines.length
                        },
                        type: 'GET',
                        dataType: 'json'
                    }).done(function (data) {
                        gettingLogInProgress = false;
                        counter = 0;
                        if (data.log.length > 0) {
                            // split the data.log with \n
                            const lines = data.log.split("\n");
                            // remove the last line, because it's empty
                            lines.pop();

                            // add the lines to the logLines array
                            logLines.push(...lines);

                            if (lines.length > 0) {
                                // add the lines to the output div
                                document.getElementById("output").innerHTML += "<br>" + lines.join("<br>");
                                dotCounter = 0;
                            }
                        } else {
                            printDot();
                        }

                        // see if the data.running is false stop the interval
                        if (!data.running) {
                            clearInterval(upgradeInterval);
                            upgradeInProgress = false;
                            toggleLoading();
                        }
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        gettingLogInProgress = false;
                        counter += 1;
                        printDot();
                    });
                }, 2000);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                upgradeInProgress = false;
                toggleLoading();

                document.getElementById("output").innerHTML += "Another upgrade in progress! Try again in a minute!<br>";
                dotCounter = 0;
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
