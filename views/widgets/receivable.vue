<template>
    <apexchart width="100%" height="120px" type="area" :options="options" :series="series"></apexchart>
</template>

<script>
export default {
    data() {
        return {
            options: {
                chart: {
                    id: 'vuechart-receivable',
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
                labels: [...Array(11).keys()].map(n => `2023-09-0${n + 1}`),
                yaxis: {
                    min: 0
                },
                xaxis: {
                    type: 'datetime',
                },
                colors: ['#008FFB'],
                title: {
                    text: '1,235,312',
                    offsetX: 30,
                    style: {
                        fontSize: '24px',
                        cssClass: 'apexcharts-yaxis-title'
                    }
                },
                subtitle: {
                    text: 'Receivable',
                    offsetX: 30,
                    style: {
                        fontSize: '14px',
                        cssClass: 'apexcharts-yaxis-title'
                    }
                }
            },
            series: [{
                name: 'Profit',
                data: [70, 30, 40, 45, 50, 49, 60, 70, 91, 20, 30]
            }]

        };
    }
};
</script>