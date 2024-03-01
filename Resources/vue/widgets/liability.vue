<template>
    <div class="bg-white relative shadow rounded p-3">
        <div class="flex items-top justify-between">
            <div>
                <span
                    class="!text-[0.8rem]  !w-[2.5rem] !h-[2.5rem] !leading-[2.5rem] !rounded-full inline-flex items-center justify-center bg-primary">
                    <i class="fas fa-user text-[1rem] text-white"></i>
                </span>
            </div>
            <div class="flex-grow ms-4">
                <div class="flex items-center justify-between flex-wrap">
                    <div>
                        <p class="text-[#8c9097] dark:text-white/50 text-[0.813rem] mb-0">Total Liability</p>
                        <h4 class="font-semibold  text-[1.5rem] !mb-2 ">{{ total }}</h4>
                    </div>
                    <div id="crm-total-customers" style="min-height: 40px;">
                        <apexchart width="100px" height="40px" type="line" :options="options" :series="series">
                        </apexchart>
                    </div>
                </div>
                <div class="flex items-center justify-between !mt-1">
                    <div> <a class="text-primary text-[0.813rem]" href="javascript:void(0);">View All<i
                                class="ti ti-arrow-narrow-right ms-2 font-semibold inline-block"></i></a> </div>
                    <div class="text-end">
                        <p class="mb-0 text-success text-[0.813rem] font-semibold">+40%</p>
                        <p class="text-[#8c9097] dark:text-white/50 opacity-[0.7] text-[0.6875rem]">this month</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    created() {
        var t = this;

        window.axios
            .get("/chart_of_account/summation/liability")
            .then((response) => {
                t.debit = response.data.debit;
                t.credit = response.data.credit;
                t.total = response.data.total;

                t.options = {
                    /* title: {
                         text: t.total.toString(),
                     },*/
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
                    id: 'vuechart-liability',
                    sparkline: {
                        enabled: true
                    },
                },
                fill: {
                    opacity: 1,
                },
                colors: ['#008FFB'],
                stroke: {
                    width: 1,
                    curve: 'smooth',

                },
                tooltip: {
                    x: {
                        show: false
                    },
                    fixed: {
                        enabled: false
                    },
                },

                x: {
                    show: false
                },
                y: {
                    title: {
                        formatter: function (seriesName) {
                            return ''
                        }
                    }
                },
                marker: {
                    show: false
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