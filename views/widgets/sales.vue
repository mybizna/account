<template>
    <apexchart width="100%" height="230" type="bar" :options="options" :series="series"></apexchart>
</template>

<script>
export default {
    data() {
        return {
            options: {
                chart: {
                    id: 'vuechart-stacked',
                    stacked: true,
                },
                plotOptions: {
                    bar: {
                        //columnWidth: '45%',
                    }
                },
                colors: ['#00D8B6', '#008FFB', '#FEB019', '#FF4560', '#775DD0'],
                labels: [10, 11, 12, 13, 14, 15, 16, 17],
                xaxis: {
                    labels: {
                        show: false
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                },
                yaxis: {
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#78909c'
                        }
                    }
                },
                title: {
                    text: 'Monthly Sales',
                    align: 'left',
                    style: {
                        fontSize: '18px'
                    }
                }
            },
            series: [
                {
                    name: "Sales",
                    data: [42, 52, 16, 55, 59, 51, 45, 32],
                }, {
                    name: "Expenses",
                    data: [6, 12, 4, 7, 5, 3, 6, 4],
                }, {
                    name: "Profit",
                    data: [6, 12, 4, 7, 5, 3, 6, 4],
                }
            ]

        };
    }
};
</script>