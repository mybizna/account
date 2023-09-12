<template>
    <div class="bg-white relative shadow rounded-lg">
        <apexchart width="100%" height="240" type="bar" :options="options" :series="series"></apexchart>
    </div>
</template>

<script>
export default {
    async created() {

        // var asset = [];
        // var liability = [];

        var expense = [];
        var income = [];

        await window.axios
            .get("/chart_of_account/summation/income")
            .then((response) => {
                this.options = {
                    labels: response.data.labels
                };
                income = response.data.data;
            }).catch((response) => { });

        await window.axios
            .get("/chart_of_account/summation/expense")
            .then((response) => {
                expense = response.data.data;
            }).catch((response) => { });

        /*
        await window.axios
            .get("/chart_of_account/summation/asset")
            .then((response) => {
               
                asset = response.data.data;
            }).catch((response) => { });
    
    
     
        await window.axios
            .get("/chart_of_account/summation/liability")
            .then((response) => {
                liability = response.data.data;
            }).catch((response) => { });
            */

        this.series = [
            {
                name: "Expenses",
                data: expense,
            }, {
                name: "Income",
                data: income,
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
                labels: ['0', '1', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12',],

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
                    name: "Expenses",
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                }, {
                    name: "Income",
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                }

            ]

        };
    }
};
</script>