<template>
    <div class="card bg-gradient-to-br from-purple-500 to-indigo-600 px-4 pb-4 sm:px-5">
        <div class="flex items-center justify-between py-2 text-white">
            <h2 class="text-lg font-medium tracking-wide">Account Summary</h2>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6">
            <div>

                <div class="text-3xl font-semibold text-white">
                    ${{ total.toFixed(2) }}
                </div>
                <p class="mt-3 text-sm text-indigo-100">{{ startDate }} - {{ endDate }} </p>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:gap-5 lg:gap-6">
                <div>
                    <p class="text-indigo-100">Income</p>
                    <div class="mt-1 flex items-center space-x-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-black/20 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                            </svg>
                        </div>
                        <p class="text-base font-medium text-white">${{ income }}</p>
                    </div>

                    <a :href="'#/account/admin/invoice'"
                        class="btn mt-3 w-full border border-white/10 bg-white/20 text-white hover:bg-white/30 focus:bg-white/30">
                        Receive
                    </a>
                </div>
                <div>
                    <p class="text-indigo-100">Expense</p>
                    <div class="mt-1 flex items-center space-x-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-full bg-black/20 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                            </svg>
                        </div>
                        <p class="text-base font-medium text-white">${{ expense }}</p>
                    </div>
                    <a :href="'#/account/admin/invoice'"
                        class="btn mt-3 w-full border border-white/10 bg-white/20 text-white hover:bg-white/30 focus:bg-white/30">
                        Send
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    async created() {

        this.calculateReportPeriod();

        await window.axios
            .get("/chart_of_account/summation/income?has_chart=0")
            .then((response) => {
                this.income = response.data.total;
            }).catch((response) => { });

        await window.axios
            .get("/chart_of_account/summation/expense?has_chart=0")
            .then((response) => {
                this.expense = response.data.total;
            }).catch((response) => { });

        this.total = this.income - this.expense;

    },
    data() {
        return {
            total: 0.00,
            expense: 0.00,
            income: 0.00,
            startDate: '',
            endDate: '',
        }
    },
    methods: {
        calculateReportPeriod() {
            const currentYear = new Date().getFullYear();
            const currentMonth = new Date().getMonth(); // 0-indexed, so September is month 8
            const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
            const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0); // Setting day to 0 gets the last day of the previous month

            // Format the dates as "30/09/2023"
            this.startDate = this.formatDate(firstDayOfMonth);
            this.endDate = this.formatDate(lastDayOfMonth);
        },
        formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        },
    },
}
</script>
  
<style >
.greeting-card-trophy-wrapper {
    background-image: url('images/misc/triangle-light.png');
    background-repeat: no-repeat;
    background-position: right;
    background-size: cover;
}

.greeting-card {
    position: relative;
}

.greeting-card .greeting-card-bg {
    position: absolute;
    bottom: 0;
    right: 0;
}

.greeting-card.greeting-card-trophy {
    position: absolute;
    bottom: 10%;
    right: 8%;
}

.v-application .v-application--is-rtl .greeting-card-bg {
    right: initial;
    left: 0;
    transform: rotateY(180deg);
}

.v-application .v-application--is-rtl .greeting-card-trophy {
    left: 8%;
    right: initial;
}
</style>
  