<template>
    <apexchart width="100%" height="230" type="bar" :options="options" :series="series"></apexchart>
</template>

<script>
export default {
    created() {
        var t = this;


        window.axios
            .get("/chart_of_account/summation/asset")
            .then((response) => {
                console.log(response.data.labels);
                t.labels = response.data.labels;
                t.asset = response.data.data;
            }).catch((response) => { });

        window.axios
            .get("/chart_of_account/summation/expense")
            .then((response) => {
                t.expense = response.data.data;
            }).catch((response) => { });

        window.axios
            .get("/chart_of_account/summation/income")
            .then((response) => {
                t.income = response.data.data;
            }).catch((response) => { });

        window.axios
            .get("/chart_of_account/summation/liability")
            .then((response) => {
                t.liability = response.data.data;
            }).catch((response) => { });

        this.options = {
            labels: t.labels
        };

        this.series = [
            {
                data: t.asset,
            }, {
                data: t.expense,
            }, {
                data: t.income,
            }, {
                data: t.liability,
            }
        ];
    },
    data() {
        return {
            labels: [],
            asset: [],
            expense: [],
            income: [],
            liability: [],
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
                labels: [0],

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
                    name: "Asset",
                    data: [0],
                }, {
                    name: "Expenses",
                    data: [0],
                }, {
                    name: "Income",
                    data: [0],
                }, {
                    name: "Liability",
                    data: [0],
                }

            ]

        };
    }
};
</script>