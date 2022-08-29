if (window.PAGE_ID === "admin.pages.live-reports.index") {
    require(["jquery", "chartJs", "underscore", "price_data", "persian_number"], function (jQuery, ChartJS, _) {

            intervals();
            setInterval(function () {
                intervals();
            }, 60 * 1000);

            fetchLiveData("#previous-year-sales-amount", "/admin/api/v1/live-reports/previous-year-sales-amount");
            fetchTablesData("#monthly-categories-table", "/admin/api/v1/live-reports/monthly-categories-sales");
            fetchOverallBarChartData();
            fetchTablesData("#yearly-categories-table", "/admin/api/v1/live-reports/yearly-categories-sales");
            fetchTablesData("#previous-year-categories-table", "/admin/api/v1/live-reports/previous-year-categories-sales");

            function intervals() {
                fetchLiveData("#daily-sales-amount", "/admin/api/v1/live-reports/daily-sales-amount");
                fetchLiveData("#monthly-sales-amount", "/admin/api/v1/live-reports/monthly-sales-amount");
                fetchLiveData("#yearly-sales-amount", "/admin/api/v1/live-reports/yearly-sales-amount");
                fetchTablesData("#latest_customers", "/admin/api/v1/live-reports/latest-customers");
                fetchTablesData("#latest_payed_orders", "/admin/api/v1/live-reports/latest-payed-orders");
            }

            function fetchLiveData(containerQuery, apiUrl) {
                const amountsContainer = jQuery(containerQuery);
                if (amountsContainer.length === 0)
                    return;
                const loaderLayer = amountsContainer.find(".loader-layer");
                const priceContainer = amountsContainer.find(".price-data");
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
                                            font: {
                                                size: 12,
                                                family: "persiansans, sans-serif"
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    yAxis: {
                                        ticks: {
                                            font: {
                                                family: "persiansans, sans-serif"
                                            }
                                        }
                                    },
                                    xAxis: {
                                        ticks: {
                                            font: {
                                                family: "persiansans, sans-serif"
                                            }
                                        }
                                    },
                                }
                            },
                        };

                        const myChart = new ChartJS(
                            document.getElementById('overall-bar-chart'),
                            config
                        );

                        loaderLayer.fadeOut();
                    }
                ).fail(function (error) {

                });
            }

            function fetchTablesData(containerQuery, apiUrl) {
                const tableContainer = jQuery(containerQuery);
                if (tableContainer.length === 0)
                    return;
                const loaderLayer = tableContainer.find(".loader-layer");
                const rowTemplate = _.template(tableContainer.find(".row-template").html());
                const rowsContainer = tableContainer.find(".data-container");
                loaderLayer.fadeIn();
                jQuery.ajax({
                    url: apiUrl,
                    method: "GET"
                }).done(function (result) {
                    loaderLayer.fadeOut();
                    rowsContainer.html("");
                    let counter = 1;
                    result.data.rows.forEach(function (row) {
                        console.log(row);
                        const newRowEl = jQuery(rowTemplate({
                            ...row,
                            row_id: counter
                        }));
                        newRowEl.find(".price-data").formatPrice();
                        newRowEl.find(".numeric-data").persianNumber();
                        rowsContainer.append(newRowEl);
                        setTimeout(function () {
                            newRowEl.fadeIn();
                        }, 500 * counter);
                        counter++;
                    });
                }).fail(function (error) {
                });
            }

        }
    )
    ;
}
