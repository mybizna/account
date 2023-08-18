@extends('base::app')

@section('content')
    @csrf


    <input id="invoice_id" type="hidden" name="invoice_id" value="{{ $invoice->id }}" />

    <section class="bg-blue-50 dark:bg-blue-900 h-full h-screen">
        <div class="col-span-full md:w-4/5 lg:w-2/4 mx-auto pt-10">

            <div class="bg-white pt-4 mb-4">
                <h1 class="text-center text-2xl text-blue-800 py-3 font-extrabold"> Make Payment </h1>
                <!-- Tab links -->
                <ul
                    class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">

                    @foreach ($gateways as $g_index => $gateway)
                        @foreach ($gateway->tabs as $t_index => $tab)
                            <li class="mr-2">
                                <a href="#" onclick="openCity(event, '{{ $tab['slug'] }}')" aria-current="page"
                                    class="tablinks inline-block px-4 py-2 rounded-t-lg dark:hover:bg-gray-800 dark:hover:text-gray-300 {{ !$g_index && !$t_index ? 'text-blue-600 bg-gray-100' : '' }} ">
                                    {{ $tab['title'] }}
                                </a>
                            </li>
                        @endforeach
                    @endforeach

                </ul>

                <!-- Tab content -->
                @foreach ($gateways as $g_index => $gateway)
                    @foreach ($gateway->tabs as $t_index => $tab)
                        <div id="{{ $tab['slug'] }}" class="tabcontent {{ !$g_index && !$t_index ? 'active' : '' }} p-3"
                            style="display: {{ !$g_index && !$t_index ? 'block' : 'none' }};">
                            {!! $tab['html'] !!}
                        </div>
                    @endforeach
                @endforeach


            </div>
            <p class="text-center text-gray-500 text-xs">
                &copy;2022. All rights reserved.
            </p>

        </div>
    </section>


    <script>
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
