if (window.PAGE_ID === "admin.pages.short-link.stats") {
    require(["jquery", "template", "chartJs"], function (jQuery) {
        jQuery(function () {
            /*let array = {
                "7-19": {"United States": 4, "Iran": 6},
                "7-20": {"United States": 12, "Japan": 3},
            };*/

            const weekly_report_btn = jQuery("#weekly-report-btn");
            const monthly_report_btn = jQuery("#monthly-report-btn");
            const yearly_report_btn = jQuery("#yearly-report-btn");

            //const total_count_input = jQuery("#total-count");
            const stats_data_input = jQuery("#stats-data");

            const raw_data = JSON.parse(stats_data_input.val());
            const weekly_data = raw_data.daily;
            const monthly_data = raw_data.monthly;
            const yearly_data = raw_data.yearly;

            let data = getData(weekly_data);
            let labels = getLabels(weekly_data);

            let lineChart = new Chart(document.getElementById("line-chart"), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    label: "آمار هفتگی بازدید از لینک",
                    borderColor: "#3e95cd",
                    fill: false
                }
                ]
            },
            options: {
                title: {
                    display: true,
                    text: 'آمار بازدید از لینک'
                }
            }
            });

            weekly_report_btn.on('click', function (){
                let data = getData(weekly_data);
                let labels = getLabels(weekly_data);
                console.log(labels);
                console.log(data);
                lineChart.data.datasets = [];
                lineChart.data.labels = [];
                lineChart.data.labels = labels;
                lineChart.data.datasets.push({
                    label: "آمار هفتگی بازدید از لینک",
                    borderColor: "#3e95cd",
                    fill: false,
                    data: data,
                });
                lineChart.update();
            });

            monthly_report_btn.on('click', function (){
                let data = getData(monthly_data);
                let labels = getLabels(monthly_data);
                console.log(labels);
                console.log(data);
                lineChart.data.datasets = [];
                lineChart.data.labels = [];
                lineChart.data.labels = labels;
                lineChart.data.datasets.push({
                    label: "آمار ماهانه بازدید از لینک",
                    borderColor: "#3e95cd",
                    fill: false ,
                    data: data,
                });
                lineChart.update();
            });

            yearly_report_btn.on('click', function (){
                let data = getData(yearly_data);
                let labels = getLabels(yearly_data);
                console.log(labels);
                console.log(data);

                lineChart.data.datasets = [];
                lineChart.data.labels = [];
                lineChart.data.labels = labels;
                lineChart.data.datasets.push({
                    label: "آمار سالانه بازدید از لینک",
                    borderColor: "#3e95cd",
                    fill: false,
                    data: data,
                });
                lineChart.update();
            });

            function getData(data_array){
                let temp = [];
                let result= [];
                for(i in data_array){
                    for(j in data_array[i] ){
                        temp.push(data_array[i][j]);
                    }
                    result.push(temp.reduce((a, b) => a + b, 0));
                    temp = [];
                }
                return result;
            }

            function getLabels(data_array){
                return Object.keys(data_array);
            }
            });


    });
}