require(['jquery', 'tools', 'select2'], function (jQuery, tools) {
    if (jQuery('#export-products').length > 0) {
        let directoryListElement = jQuery('#directory-list');
        let directoryChildListElement = jQuery('#childList');
        let requestUrl = directoryListElement.data('url');

        directoryListElement.select2();
        directoryChildListElement.select2();

        directoryListElement.on('change', function (e) {
            getSubDirectoriesList(requestUrl);
        });
        directoryChildListElement.on('change', function (e) {
            if (jQuery(this).val() !== '')
                getProductsList(jQuery(this).val());
        });
        window.getSubDirectoriesList = function (route) {
            let dirId = directoryListElement.val();
            let body = {"dirId": dirId, _token: window.csrf_token};
            tools.httpRequest(route, body, 'POST', resultGetDirectoryChild, 0);

            function resultGetDirectoryChild(response) {
                if (response.length === 0)
                    getProductsList(directoryListElement.val());
                directoryChildListElement.empty();
                directoryChildListElement.append(`<option value="">انتخاب دسته بندی</option>`);
                let main = [];
                jQuery(response).each(function () {
                    let temp = {
                        "text": this.title,
                        "id": this.id
                    };
                    main.push(temp);
                });
                directoryChildListElement.select2({
                    placeholder: "دسته بندی",
                    data: main
                });
            }
        };

        window.getProductsList = function (id) {
            window.location.href = window.site_url + '/admin/product/show-product-list?dirId=' + id;
        };
        getSubDirectoriesList(requestUrl);
    }
});