<template>
    <div class="bg-white relative shadow rounded">
        <apexchart width="100%" height="80px" type="area" :options="options" :series="series"></apexchart>
    </div>
</template>

<script>
export default {
    created() {
        var t = this;

        window.axios
            .get("/chart_of_account/summation/income")
            .then((response) => {
                t.debit = response.data.debit;
                t.credit = response.data.credit;
                t.total = response.data.total;

                t.options = {
                    title: {
                        text: t.total.toString(),
                    },
                    labels: response.data.labels
                };

                t.series = [{
                    data: response.data.data
                }];

            })
            .catch((response) => {
            });
    },
    data() {
        return {
            options: {
                chart: {
                    id: 'vuechart-income',
                    sparkline: {
                        enabled: true
                    },
                },
                stroke: {
                    curve: 'straight'
                },
                fill: {
                    opacity: 1,
                },
                labels: ['0'],
                xaxis: {
                    type: 'string',
                },
                colors: ['#008FFB'],
                title: {
                    text: '0.000',
                    offsetX: 30,
                    style: {
                        fontSize: '24px',
                        cssClass: 'apexcharts-yaxis-title'
                    }
                },
                subtitle: {
                    text: 'Income',
                    offsetX: 30,
                    style: {
                        fontSize: '14px',
                        cssClass: 'apexcharts-yaxis-title'
                    }
                }
            },
            series: [{
                name: 'Amt',
                data: [0]
            }]

        };
    }
};
</script>