if (window.PAGE_ID === "admin.pages.live-reports.index") {
    require(["jquery", "chartJs", "underscore", "price_data", "persian_number"], function (jQuery, ChartJS, _) {

            intervals();
            setInterval(function () {
                intervals();
            }, 60 * 1000);

            fetchLiveData("#previous-year-sales-amount", "/admin/api/v1/live-reports/previous-year-sales-amount");
            fetchTablesData("#monthly-categories-table", "/admin/api/v1/live-reports/monthly-categories-sales");
            fetchOverallBarChartData("#overall-bar-chart", "/admin/api/v1/live-reports/overall-bar-chart-data");
            fetchOverallBarChartData("#overall-created-products-per-category", "/admin/api/v1/live-reports/overall-created-products-per-category");
            fetchOverallBarChartData("#overall-sales-bar-chart", "/admin/api/v1/live-reports/overall-sales-bar-chart-data");
            fetchOverallBarChartData("#categories-availability", "/admin/api/v1/live-reports/categories-availability");
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

            function fetchOverallBarChartData(containerQuery, apiUrl) {
                const overallBarChartContainer = jQuery(containerQuery);
                if (overallBarChartContainer.length === 0)
                    return;
                const loaderLayer = overallBarChartContainer.find(".loader-layer");
                loaderLayer.fadeIn();
                jQuery.ajax({
                    url: apiUrl,
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
                            overallBarChartContainer.find("canvas"),
                            config
                        );

                        loaderLayer.fadeOut();
                    }
                ).fail(function (error) {

                });
            }

            function fetchTablesData(containerQuery, apiUrl) {
                const tableContainer = jQuery(containerQuery);
                const fullColorSet = [
                    "rgb(249, 65, 68)",
                    "rgb(243, 114, 44)",
                    "rgb(248, 150, 30)",
                    "rgb(249, 199, 79)",
                    "rgb(144, 190, 109)",
                    "rgb(67, 170, 139)",
                    "rgb(142, 202, 230)",
                    "rgb(18, 103, 130)",
                    "rgb(2, 48, 71)",

                ];
                if (tableContainer.length === 0)
                    return;
                const loaderLayer = tableContainer.find(".loader-layer");
                const rowTemplate = _.template(tableContainer.find(".row-template").html());
                const rowsContainer = tableContainer.find(".data-container");
                const chartContainer = tableContainer.find(".chart-container canvas");
                loaderLayer.fadeIn();
                jQuery.ajax({
                    url: apiUrl,
                    method: "GET"
                }).done(function (result) {
                    loaderLayer.fadeOut();
                    rowsContainer.html("");
                    let counter = 1;
                    result.data.rows.forEach(function (row) {
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

                    if (chartContainer.length > 0) {
                        const config = {
                            type: 'pie',
                            data: {
                                labels: result.data.rows.map((iterRow) => (iterRow.title)),
                                datasets: [{
                                    backgroundColor: fullColorSet.slice(0, result.data.rows.length),
                                    data: result.data.rows.map((iterRow) => (iterRow.total_amount)),
                                    hoverOffset: 4
                                }],
                            }
                        };
                        const pieChart = new ChartJS(
                            chartContainer,
                            config
                        );
                    }
                }).fail(function (error) {
                });
            }

        }
    )
    ;
}
