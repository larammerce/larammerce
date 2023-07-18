if (window.PAGE_ID === "admin.pages.upgrade.index")
    require(["jquery"], function (jQuery) {

        function handleUpgrade(url) {
            const source = new EventSource(url);

            source.onmessage = function(event) {
                // Close the connection if the server sends a specific end signal.
                if (event.data.startsWith('END:')) {
                    source.close();
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
