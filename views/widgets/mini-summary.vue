<template>
    <div class="card bg-gradient-to-br from-purple-500 to-indigo-600 px-4 pb-4 sm:px-5">
        <div class="flex items-center justify-between py-3 text-white">
            <h2 class="text-sm+ font-medium tracking-wide">Your Balance</h2>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6">
            <div>
                <div class="flex w-9/12 items-center space-x-1">
                    <p class="text-xs text-indigo-100 line-clamp-1">
                        0x9CDBC28F0A6C13BB42ACBD3A3B366BFCAB07B8B1
                    </p>
                    <button
                        class="btn h-5 w-5 shrink-0 rounded-full p-0 text-white hover:bg-white/20 focus:bg-white/20 active:bg-white/25">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z"></path>
                            <path d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5h8a2 2 0 00-2-2H5z"></path>
                        </svg>
                    </button>
                </div>
                <div class="mt-3 text-3xl font-semibold text-white">
                    $5,566.00
                </div>
                <p class="mt-3 text-xs+ text-indigo-100">11.159849849 BTC</p>
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
                        <p class="text-base font-medium text-white">$2,225.22</p>
                    </div>

                    <button
                        class="btn mt-3 w-full border border-white/10 bg-white/20 text-white hover:bg-white/30 focus:bg-white/30">
                        Receive
                    </button>
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
                        <p class="text-base font-medium text-white">$225.22</p>
                    </div>
                    <button
                        class="btn mt-3 w-full border border-white/10 bg-white/20 text-white hover:bg-white/30 focus:bg-white/30">
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
  
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
}</style>
  