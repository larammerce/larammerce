if (window.PAGE_ID === "admin.pages.live-reports.index") {
    require(["jquery", "chartJs", "price_data"], function (jQuery, ChartJS) {

        intervals();
        setInterval(function () {
            intervals();
        }, 30000);

        fetchLiveData("#previous-year-sales-amount", "/admin/api/v1/live-reports/previous-year-sales-amount");
        fetchOverallBarChartData();

        function fetchLiveData(containerQuery, apiUrl) {
            const dailySalesAmountContainer = jQuery(containerQuery);
            if (dailySalesAmountContainer.length === 0)
                return;
            const loaderLayer = dailySalesAmountContainer.find(".loader-layer");
            const priceContainer = dailySalesAmountContainer.find(".price-data");
            loaderLayer.fadeIn();
            jQuery.ajax({
                url: apiUrl,
                method: "GET"
            }).done(function (result) {
                priceContainer.text(result.data.amount);
                priceContainer.formatPrice();
                loaderLayer.fadeOut();
            }).fail(function (error) {

            });
        }

        function intervals() {
            fetchLiveData("#daily-sales-amount", "/admin/api/v1/live-reports/daily-sales-amount");
            fetchLiveData("#monthly-sales-amount", "/admin/api/v1/live-reports/monthly-sales-amount");
            fetchLiveData("#yearly-sales-amount", "/admin/api/v1/live-reports/yearly-sales-amount");
        }

        function fetchOverallBarChartData() {
            const overallBarChartContainer = jQuery("#overall-bar-chart-container");
            if (overallBarChartContainer.length === 0)
                return;
            const loaderLayer = overallBarChartContainer.find(".loader-layer");
            loaderLayer.fadeIn();
            jQuery.ajax({
                url: "/admin/api/v1/live-reports/overall-bar-chart-data",
                method: "GET"
            }).done(function (result) {
                const labels = result.data.labels;
                const data = {
                    labels: labels,
                    datasets: result.data.datasets
                };

                const config = {
                    type: 'bar',
                    data: data,
                    options: {
                        aspectRatio: 5,
                        plugins: {
                            legend: {
                                labels: {
                                    // This more specific font property overrides the global property
                                    font: {
                                        size: 12,
                                        family: "persiansans, sans-serif"
                                    }
                                }
                            }
                        },
                        scales: {
                            yAxes: [{
                                pointLabels: {
                                    font: {
                                        family: "persiansans, sans-serif"
                                    }
                                }
                            }],
                            xAxes: [{
                                font: {
                                    family: "persiansans, sans-serif"
                                }
                            }],
                        }
                    },
                };

                const myChart = new ChartJS(
                    document.getElementById('overall-bar-chart'),
                    config
                );

                loaderLayer.fadeOut();
            }).fail(function (error) {

            });
        }

        function fetchTablesData(){

        }

    });
}
