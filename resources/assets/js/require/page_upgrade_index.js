if (window.PAGE_ID === "admin.pages.upgrade.index")
    require(["jquery"], function (jQuery) {
        var csrfToken = window.csrf_token; // Make sure to initialize csrf_token globally

        function handleUpgrade(url) {
            var outputElement = jQuery('#output');
            outputElement.text(''); // Clear the output for a new process

            jQuery.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function () {
                    var eventSource = new EventSource(url);

                    eventSource.onmessage = function (event) {
                        outputElement.append(event.data + "\n");
                    };

                    eventSource.onerror = function (err) {
                        console.error("EventSource failed:", err);
                        eventSource.close();
                    };
                },
                error: function (err) {
                    console.error(err);
                }
            });
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