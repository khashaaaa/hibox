var SiteSpeedPage = Backbone.View.extend({
    "el": $("#site-speed-view-wrapper")[0],
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        this.initializeChartCurrentBoxStatistics();
        this.initializeChartArchiveBoxStatistics();
        this.render();
    },

    getCurrentBoxStatistics: function (chartSiteSpeed)
    {
        $.ajax({
            url: '?cmd=Reports&do=getCurrentBoxStatistics',
            success: function(data) {
                chartSiteSpeed.data.labels.push('');
                chartSiteSpeed.data.datasets[0].data.push(data.boxFullTime);
                chartSiteSpeed.data.datasets[1].data.push(data.apiNetworkTimePerCall);

                chartSiteSpeed.update();
            }
        });
    },

    getArchiveBoxStatistics: function (chartSiteSpeedArchive)
    {
        $.ajax({
            url: '?cmd=Reports&do=getBoxArchiveStatistics',
            success: function(data) {
                for(var i = 0; i < data.length; i++)
                {
                    chartSiteSpeedArchive.data.labels.push(data[i].Time);
                    chartSiteSpeedArchive.data.datasets[0].data.push(data[i].BoxFullTime);
                    chartSiteSpeedArchive.data.datasets[1].data.push(data[i].BoxAverageTime);
                    chartSiteSpeedArchive.data.datasets[2].data.push(data[i].ApiWorktime);
                    chartSiteSpeedArchive.data.datasets[3].data.push(data[i].ApiNetworkTime);
                    chartSiteSpeedArchive.update();
                }
            }
        });
    },

    initializeChartCurrentBoxStatistics: function ()
    {
        let self = this;

        var chartSiteSpeed = new Chart(
            document.getElementById('siteSpeed'),
            {
                type: 'line',
                data: {
                    labels: ['',''],
                    datasets: [
                        {
                            data: [],
                            borderColor: '#ff0303',
                            backgroundColor: '#ff0303',
                            label: trans.get('Average_time_page_generation'),
                        },
                        {
                            data: [],
                            borderColor: '#00c0ef',
                            backgroundColor: '#00c0ef',
                            label: trans.get('Average_time_api_connect'),
                        }
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    title: {
                        display: true,
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: false,
                            }
                        }]
                    }
                }
            }
        );

        $('#siteSpeed').css("height",500);


        if ((chartSiteSpeed.data.datasets[0].data.length === 0) && (chartSiteSpeed.data.datasets[1].data.length === 0)) {
            self.getCurrentBoxStatistics(chartSiteSpeed);
        }

        let timer = 60, minutes, seconds;
        let display = document.querySelector('#time');

        setInterval(function()
        {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--timer < 0) {
                self.getCurrentBoxStatistics(chartSiteSpeed);
                timer = 60;
            }
        }, 1000);
    },

    initializeChartArchiveBoxStatistics: function ()
    {
        var chartSiteSpeedArchive = new Chart(
            document.getElementById('siteSpeedPerMonth'),
            {
                type: 'line',
                data: {
                    datasets: [
                        {
                            data: [],
                            borderColor: '#ff0303',
                            backgroundColor: '#ff0303',
                            label: trans.get('Average_time_page_generation'),
                        },
                        {
                            data: [],
                            borderColor: '#d700ef',
                            backgroundColor: '#d700ef',
                            label: trans.get('Average_time_box'),
                        },
                        {
                            data: [],
                            borderColor: '#40e514',
                            backgroundColor: '#40e514',
                            label: trans.get('Average_time_api_work'),
                        },
                        {
                            data: [],
                            borderColor: '#00c0ef',
                            backgroundColor: '#00c0ef',
                            label: trans.get('Average_time_api_connect'),
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                }
            }
        );
        let self = this;
        self.getArchiveBoxStatistics(chartSiteSpeedArchive);
    }
});

$(function(){
    new SiteSpeedPage();
});
