<template>
    <apexchart ref="realtimeChart" width="100%" height="120px" type="area" :options="options" :series="series"></apexchart>
</template>

<script>
export default {
    created() {
        var t = this;

        window.axios
            .get("/chart_of_account/summation/expense")
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
            debit: 0.00,
            credit: 0.00,
            total: 0.00,
            options: {
                chart: {
                    id: 'vuechart-expense',
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
                    text: 'Expenses',
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