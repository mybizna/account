@extends('base::app')

@section('content')
    @csrf

    <section class="bg-blue-50 dark:bg-blue-900 h-full h-screen">
        <div class="w-full md:w-4/5 lg:w-2/4 mx-auto pt-10">

            <div class="bg-white pt-6 mb-4">
                <h1 class="text-center text-2xl text-blue-800 py-3 font-extrabold"> Make Payment </h1>
                <!-- Tab links -->

                <ul
                    class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">

                    @foreach ($gateways as $gateway)
                        @foreach ($gateway->tabs as $tab)
                            <li class="mr-2">
                                <a href="#" onclick="openCity(event, {{ $tab['slug'] }})" aria-current="page"
                                    class="tablinks inline-block p-4 rounded-t-lg dark:hover:bg-gray-800 dark:hover:text-gray-300 text-blue-600 bg-gray-100 ">
                                    {{ $tab['title'] }}
                                </a>
                            </li>
                            <p>This is user {{ $user->id }}</p>
                        @endforeach
                    @endforeach
                    
                </ul>


                <!-- Tab content -->
                <div id="STKPUSH" class="tabcontent active p-3" style="display: block">
                    <form method="POST" action="{{ url(route('isp_access_stkpush')) }}">
                        @csrf

                        <h2 class="text-xl text-center pb-3">MPesa STK Push</h2>

                        <p class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Please Enter you <b
                                class="text-green-500">Phone Number</b> that you would like us to send <b
                                class="text-green-500">MPESA STK Push</b>.</p>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                                Phone Number
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="phone" value="{{ $phone }}" @if ($request_sent) readonly @endif
                                name="phone" type="text" placeholder="Phone">
                        </div>

                        @if ($request_sent)
                            <div class="text-center">
                                <input type="hidden" name="verifying" value="1">
                                <b>After receiving the payment confirmation message,press "Verify Payment" to finish
                                    making payment.</b>
                                <button id='stkpush' type="submit" name="view" value="stkpush_{{ $invoice->id }}"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Verify Payment
                                </button>
                            </div>
                        @else
                            <div class="text-center">
                                <button id='stkpush' type="submit" name="view" value="stkpush_{{ $invoice->id }}"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Send STK Push
                                </button>
                            </div>
                        @endif


                    </form>
                </div>

                <div id="TILLNO" class="tabcontent p-3" style="display: none">
                    <form method="POST" action="{{ url(route('isp_access_tillno')) }}">
                        @csrf

                        <h2 class="text-xl text-center pb-3">TILL No</h2>

                        <p class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Please follow the steps shown
                            below to make your payment using MPesa:</p>
                        <ul class="max-w-md space-y-1 text-gray-800 list-inside dark:text-gray-600">
                            <li class="flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1.5 text-green-500 dark:text-green-400 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p>ONLY <b class="text-green-700">SAFARICOM/MPESA</b> Accepted.</p>
                            </li>
                            <li class="flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1.5 text-green-500 dark:text-green-400 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p>Please Go to <b class="text-green-700">LIPA na MPesa</b></p>
                            </li>
                            <li class="flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1.5 text-green-500 dark:text-green-400 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p>Select <b class="text-green-700">Buy Goods and Services</b></p>
                            </li>
                            <li class="flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1.5 text-green-500 dark:text-green-400 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p>Send to Till Number <b class="text-green-700">{{ $gateway->till_bill_no }}</b></p>
                            </li>
                        </ul>

                        <div class="mb-4">
                            <br>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="mpesa_code">
                                MPESA CODE
                            </label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="mpesa_code" name="mpesa_code" type="text" placeholder="MPesa Code">
                        </div>
                        <div style="text-align:center; margin-top:20px;">
                            <button id='tillno' type="submit" name="view" value="tillno_{{ $invoice->id }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Verify MPesa Code
                            </button>
                        </div>
                    </form>
                </div>

                <div id="PAYBILL" class="tabcontent p-3" style="display: none">
                    <form method="POST" action="{{ url(route('isp_access_paybill')) }}">
                        @csrf

                        <h2 class="text-xl text-center pb-3">Paybill No</h2>

                        <p class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Please follow the steps shown
                            below to make your payment using MPesa:</p>
                        <ul class="max-w-md space-y-1 text-gray-800 list-inside dark:text-gray-600">
                            <li class="flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1.5 text-green-500 dark:text-green-400 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p>ONLY <b class="text-green-700">SAFARICOM/MPESA</b> Accepted.</p>
                            </li>
                            <li class="flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1.5 text-green-500 dark:text-green-400 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p>Please Go to <b class="text-green-700">LIPA na MPesa</b></p>
                            </li>
                            <li class="flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1.5 text-green-500 dark:text-green-400 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p>Select <b class="text-green-700">Paybill</b></p>
                            </li>
                            <li class="flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1.5 text-green-500 dark:text-green-400 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p>Send to Paybill Number <b class="text-green-700">{{ $gateway->till_bill_no }}</b></p>
                            </li>

                            <li class="flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1.5 text-green-500 dark:text-green-400 flex-shrink-0"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p>
                                    Send to Account Name <b class="text-green-700">{{ $user->username }}</b>
                                </p>
                            </li>
                        </ul>


                        <div style="text-align:center; margin-top:20px;">
                            <button id='paybill' type="submit" name="view" value="paybill_{{ $invoice->id }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Verify MPesa
                            </button>
                        </div>
                    </form>
                </div>




            </div>
            <p class="text-center text-gray-500 text-xs">
                &copy;2022. All rights reserved.
            </p>

        </div>
    </section>


    <script>
        var input = document.querySelector("#phone");
        window.intlTelInput(input, {
            // any initialisation options go here
            onlyCountries: ["ke"],
            separateDialCode: true,
        });

        function openCity(evt, methodName) {
            // Declare all variables
            var i, tabcontent, tablinks;
            // Get all elements with class="tabcontent" and hide them
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            // Get all elements with class="tablinks" and remove the class "active"
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                console.log(tablinks[i].className);
                tablinks[i].className = tablinks[i].className.replace("text-blue-600", "");
                tablinks[i].className = tablinks[i].className.replace("bg-gray-100", "");
                console.log(tablinks[i].className);
                console.log("--------------------");
            }

            // Show the current tab, and add an "active" class to the button that opened the tab
            document.getElementById(methodName).style.display = "block";
            evt.currentTarget.className += " text-blue-600";
            evt.currentTarget.className += " bg-gray-100";
        }
    </script>
@endsection
