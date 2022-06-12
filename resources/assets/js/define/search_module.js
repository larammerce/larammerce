define('search_module', ['jquery', 'underscore', 'template'], function (jQuery, _, template) {
    var SearchModule;
    var config = {
        mainContainerSelector: '.main-content',
        inputSelector: "#search-input",
        mobileButtonSelector: "#search-button",
        resultRowSelector: ".result-row",
        exitButtonSelector: "button.exit-button",
        loadingSelector: "#loading-animation",
        itemShowDelay: 100,
        searchUrls: [
            '/admin/api/v1/product/query',
            '/admin/api/v1/user/query',
            '/admin/api/v1/directory/query',
            '/admin/api/v1/invoice/query'
        ]
    };

    var mainContainer, resultPageEl, resultContainerEl, inputEl, mobileButtonEl, exitButtonEl, loadingEl, results,
        isRenderingResults;

    function getItemType(_item) {
        if (_item.hasOwnProperty('class_name')) {
            var classNameParts = _item['class_name'].split("\\");
            return classNameParts[classNameParts.length - 1].toLowerCase();
        }
        return false;
    }

    function getItemImage(_item, type = "none") {
        if (_item.hasOwnProperty('main_photo')) {
            return _item["main_photo"];
        } else if (type === "invoice") {
            return '/admin_dashboard/images/icons/invoice.png';
        }
        return '/admin_dashboard/images/No_image.jpg.png';
    }

    function convertItemData(_item) {
        let type = getItemType(_item);
        let title = "";
        if (type === "user") {
            title = _item["text"];
        } else if (type === "invoice") {
            title = `فاکتور ${_item["transferee_name"]} - ${_item["created_at_jalali"]}`;
        } else {
            title = _item["title"];
        }
        return {
            title: title,
            link: _item["search_url"],
            type: type,
            image: getItemImage(_item, type)
        };
    }


    return {
        init: function (_customConfig) {
            SearchModule = this;

            config = _.extend(config, _customConfig);
            isRenderingResults = false;
            results = [];

            SearchModule.initElements();
            SearchModule.bindInputEl();
            SearchModule.bindExitButtonEl();
        },
        initElements: function () {
            //initiating main container element
            mainContainer = jQuery(config.mainContainerSelector);

            //initiating result page element
            resultPageEl = jQuery(template.searchContainer({}));
            resultPageEl.isActivated = false;
            resultPageEl.makeActivated = function () {
                if (!this.isActivated) {
                    this.isActivated = true;
                    this.fadeIn();
                    return true;
                }
                return false;
            };
            resultPageEl.makeDeactivated = function () {
                if (this.isActivated) {
                    this.isActivated = false;
                    this.fadeOut();
                    return true;
                }
                return false;
            };
            mainContainer.append(resultPageEl);

            //initiating result container element
            resultContainerEl = resultPageEl.find(config.resultRowSelector);
            resultContainerEl.clearItems = function () {
                this.html("");
            };

            //initiating search input element
            inputEl = jQuery(config.inputSelector);
            inputEl.makeEmpty = function () {
                this.val('');
            };

            //initiating mobile search button element
            mobileButtonEl = jQuery(config.mobileButtonSelector);

            //initiating exit button element
            exitButtonEl = jQuery(config.exitButtonSelector);

            //initiating loading gif element
            loadingEl = jQuery(config.loadingSelector);
            loadingEl.requestsCount = 0;
            loadingEl.isLoading = false;
            loadingEl.showLoading = function () {
                if (!this.isLoading) {
                    this.isLoading = true;
                    this.fadeIn();
                }
            };
            loadingEl.hideLoading = function () {
                if (this.isLoading) {
                    this.isLoading = false;
                    this.requestsCount = 0;
                    this.fadeOut();
                }
            };
            loadingEl.requestAdded = function () {
                this.requestsCount++;
                this.showLoading();
            };
            loadingEl.requestFinished = function () {
                if (this.requestsCount > 0)
                    this.requestsCount--;
                if (this.requestsCount === 0)
                    this.hideLoading();
            };
        },
        bindInputEl: function () {
            inputEl.actionTimout = null;
            inputEl.on('keyup keydown', function (_event) {
                clearTimeout(inputEl.actionTimout);

                if (inputEl.val().length > 3) {
                    resultPageEl.makeActivated();
                    inputEl.actionTimout = setTimeout(function () {
                        SearchModule.doSearch(inputEl.val());
                    }, 1000);
                } else {
                    resultPageEl.makeDeactivated();
                    SearchModule.clearSearchResults();
                }
            });
        },
        bindExitButtonEl: function () {
            exitButtonEl.on('click', function (_event) {
                _event.preventDefault();
                _event.stopPropagation();
                inputEl.makeEmpty();
                resultPageEl.makeDeactivated();
                SearchModule.clearSearchResults();

                return false;
            });
        },
        doSearch: function (_query) {
            SearchModule.clearSearchResults();

            _.each(config.searchUrls, function (_searchUrl, _searchUrlIndex) {
                loadingEl.requestAdded();

                jQuery.ajax({
                    url: _searchUrl,
                    method: 'GET',
                    data: {
                        query: _query
                    }
                }).success(function (_result) {
                    if (_result.data && _result.data.collection)
                        _.each(_result.data.collection, function (_item, _itemIndex) {
                            SearchModule.addSearchResult(_item);
                        });
                    loadingEl.requestFinished();
                }).error(function (_result) {
                    console.error(_result);
                    loadingEl.requestFinished();
                });
            });
        },
        addSearchResult: function (_item) {
            results.push(_item);
            SearchModule.renderResults();
        },
        renderResults: function () {
            if (!isRenderingResults) {
                isRenderingResults = true;

                resultContainerEl.pushInterval = setInterval(
                    function () {
                        if (results.length > 0) {
                            var item = results.pop();
                            var itemEl = jQuery(template.searchResultItem(convertItemData(item)));
                            resultContainerEl.append(itemEl);

                        } else {
                            clearInterval(resultContainerEl.pushInterval);
                            isRenderingResults = false;
                        }
                    }, config.itemShowDelay
                );
            }
        },
        clearSearchResults: function () {
            results = [];
            resultContainerEl.clearItems();
        }
    }
});