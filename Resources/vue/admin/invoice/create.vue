<template>
    <edit-render :path_param="['account', 'invoice']" :model="model" passed_form_url="invoice/savedata">

        <div class="row mb-2">
            <div class="col-sm-6">
                <TextElement name="title" label="Invoice Title" id="title" :debounce="500" rules="required" />
            </div>
            <div class="col-sm-6">
                <RecordpickerElement name="partner_id" :valdata="model['partner_id']" :label="Select Partner"
                    id="partner_id" :setting=" setting " />
            </div>

        </div>

        <div v-if=" has_partner "
            class="relative invoice-form p-1 border border-dotted border-dashed border-green-600 rounded overflow-hidden">

            <div style="margin-right: -45px; !important" class="absolute w-48 z-10 p-1 top-7 right-0 rotate-45"
                :class=" getStatusClass ">
                <h3
                    class="text-center p-1 uppercase font-semibold text-white  text-xl border-b border-t border-dashed border-gray-50">
                    {{ model.status }} </h3>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <span class="underline">From</span>
                    <address>
                        <strong>{{ company.name }} </strong><br>
                        {{ company.address }} {{ company.postal_code }}<br>
                        {{ company.city }}, {{ company.country }}<br>
                        Phone: {{ company.phone }} &nbsp; {{ company.mobile }}<br>
                        Email: {{ company.email }}
                    </address>
                </div>
                <div class="col-md-4">
                    <span class="underline">To</span>
                    <address v-if=" partner ">
                        <template v-if=" partner.first_name || partner.last_name ">
                            <strong>{{ partner.first_name }} {{ partner.last_name }}</strong><br>
                        </template>
                        <template v-if=" partner.company ">
                            <strong>{{ partner.company }} </strong><br>
                        </template>
                        <template v-if=" partner.address || partner.postal_code ">
                            {{ partner.address }} {{ partner.postal_code }}<br>
                        </template>
                        <template v-if=" partner.city || partner.country ">
                            {{ partner.city }}, {{ partner.country }}<br>
                        </template>
                        <template v-if=" partner.phone || partner.mobile ">
                            Phone: {{ partner.phone }} &nbsp; {{ partner.mobile }}<br>
                        </template>
                        <template v-if=" partner.email ">
                            Email: {{ partner.email }}
                        </template>
                    </address>

                </div>
                <div class="col-md-4">

                    <b v-if=" invoice.created_at ">Invoice #{{ invoice.created_at }}</b>
                    <b v-else>Invoice #{{ invoice.id }}</b>
                    <br>
                    <br>
                    <b>Created On:</b> {{ timestamp }}
                    <br>
                    <b> Due Date:</b>
                    <DateElement name="due_date" id="due_date" :date=" true " :time=" true " :debounce=" 500 "
                        rules="required" />
                </div>
            </div>

            <table class="table m-0 p-0">
                <thead>
                    <tr class="bg-slate-100 px-7">
                        <th class="uppercase" scope="col">Title</th>
                        <th class="uppercase" scope="col">Ledger</th>
                        <th class="uppercase" scope="col">Qtry</th>
                        <th class="uppercase" scope="col">Price</th>
                        <th class="uppercase" scope="col">Rates</th>
                        <th class="uppercase" scope="col">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(            item, index           ) in            model.items           " :key=" index ">
                        <td>
                            <TextElement name="title" label="Title" id="title" :debounce=" 500 " rules="required" />
                        </td>
                        <td>
                            <SelectElement name="ledger_id" :items=" ledgers " id="ledger_id" :debounce=" 500 "
                                rules="required" />
                        </td>
                        <td class="w-28">
                            <TextElement name="quantity" id="quantity" input-type="number" min="0" :debounce=" 500 "
                                rules="required" @blur="addCalculate(rate)" />
                        </td>
                        <td>
                            <TextElement name="price" id="price" input-type="number" min="0" step="0.01"
                                :debounce=" 500 " rules="required" @blur="addCalculate(rate)" />
                        </td>
                        <td>
                            <span v-for="(            item_rate, rate_index           ) in            item.rates           "
                                :key=" rate_index " class="badge bg-secondary mr-1">{{ item_rate.title }} ({{
                                item_rate.value }}<span v-if=" item_rate.is_percent ">%</span>)</span>
                            <a class="badge bg-blue-700 text-white cursor-pointer" data-bs-toggle="modal"
                                :data-bs-target=" '#' + 'Modal' + index ">
                                <i class="fa-solid fa-plus"></i> Add Rate
                            </a>

                            <div class="modal fade" :id=" 'Modal' + index " tabindex="-1"
                                :aria-labelledby=" index + 'ModalLabel' " aria-hidden="true">
                                <div class="modal-dialog ">
                                    <div class="modal-content shadow-2xl shadow-indigo-500/50">
                                        <div class="modal-header p-2">
                                            <h5 class="modal-title font-semibold" :id=" index + 'ModalLabel' ">Select
                                                Rate</h5>
                                            <button type="button" class="" data-bs-dismiss="modal" aria-label="Close">
                                                <i class="fa-solid fa-circle-xmark text-2xl	text-red"></i>
                                            </button>
                                        </div>

                                        <div class="modal-body p-0">
                                            <table class="table m-0 p-0">
                                                <thead>
                                                    <tr class="bg-slate-100 px-7">
                                                        <th class="uppercase" scope="col"></th>
                                                        <th class="uppercase" scope="col">Title</th>
                                                        <th class="uppercase" scope="col">Value</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(            rate, r_index           ) in            rates           "
                                                        :key=" r_index ">
                                                        <td>
                                                            <a v-if=" item.rate_ids.includes(rate.id) "
                                                                class="btn btn-danger btn-sm">Remove</a>
                                                            <a v-else class="btn btn-primary btn-sm"
                                                                @click="addRate(index, item, rate)">Add</a>
                                                        </td>
                                                        <td>
                                                            {{ rate.title }}
                                                        </td>
                                                        <td>
                                                            {{ rate.value }} <span v-if=" rate.is_percent ">%</span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="font-semibold fs-16 text-right">{{ this.$func.money(item.total) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="text-center mt-2">
                <a class="btn btn-primary btn-sm cursor-pointer" @click="addRow()">
                    <i class="fa-solid fa-plus"></i> Add Row
                </a>
            </div>

            <div class="row mt-3">

                <div class="col-6">
                    <p class="lead">Payment Methods:</p>

                    <div>
                        <ul class="nav nav-tabs" id="myPayment" role="tablist">
                            <li v-for="(           gateway, g_index           ) in            model.gateways           "
                                v-bind:key=" g_index " class="nav-item" role="presentation">
                                <button :class=" !g_index ? 'nav-link active' : 'nav-link' "
                                    :id=" gateway.slug + '-tab' " data-bs-toggle="tab"
                                    :data-bs-target=" '#' + gateway.slug " type="button" role="tab"
                                    :aria-controls=" gateway.slug " :aria-selected=" !g_index ? 'true' : 'false' ">
                                    <i v-if=" gateway.paid_amount > 0 " class="fas fa-check-circle"></i>
                                    {{
                                    gateway.title
                                    }}</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myPaymentContent">
                            <div v-for="(           gateway, g_index           ) in            model.gateways           "
                                v-bind:key=" g_index "
                                :class=" !g_index ? 'tab-pane fade show active' : 'tab-pane fade' " :id=" gateway.slug "
                                role="tabpanel" :aria-labelledby=" gateway.slug + '-tab' ">
                                <div class="p-2">
                                    <TextElement name="amount" label="Amount" id="amount" input-type="number"
                                        v-model=" gateway.amount " :debounce=" 500 " rules="required"
                                        @keyup=" calculateTotal " />

                                    <template v-if=" gateway.slug != 'cash' ">
                                        <TextElement name="reference" label="Reference" id="reference"
                                            v-model=" gateway.reference " :debounce=" 500 " rules="required" />

                                        <TextElement name="others" label="Others" id="others" v-model=" gateway.others "
                                            :debounce=" 500 " rules="required" />

                                    </template>

                                    <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                                        {{ gateway.instruction }}
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>


                </div>

                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="width:60%">Subtotal:</th>
                                    <td class="text-right font-semibold">{{ this.$func.money(model.subtotal) }}</td>
                                </tr>
                                <template v-for="( rate, index ) in  rates " :key="index">
                                    <tr v-if=" rate.total > 0 || rate.total < 0 ">
                                        <th>{{ rate.title }} (<span
                                                v-if=" rate.method == '-' || rate.method == '-%' ">-</span>{{ rate.value
                                            }}<span v-if=" rate.method == '-%' || rate.method == '+%' ">%</span>)
                                        </th>
                                        <td class="text-right font-semibold">{{ this.$func.money(rate.total) }}</td>
                                    </tr>
                                </template>
                                <tr>
                                    <th>Total:</th>
                                    <td class="text-right font-semibold">{{ this.$func.money(model.total) }}</td>
                                </tr>

                            </tbody>
                        </table>

                        <table class="table">
                            <tbody>
                                <tr>
                                    <th colspan="10">Payment:</th>
                                </tr>
                                <template
                                    v-for="(           gateway, g_index           ) in            model.gateways           "
                                    v-bind:key="g_index">
                                    <tr v-if=" gateway.paid_amount > 0 ">
                                        <th>{{ gateway.title }}</th>
                                        <td>{{ timestamp }}</td>
                                        <td>{{ gateway.reference }}</td>
                                        <td class="text-right font-semibold">{{ this.$func.money(gateway.paid_amount) }}
                                            {{ gateway.paid_amount }}
                                        </td>
                                    </tr>
                                </template>
                                <tr v-if=" model.balance > 0 " class="bg-red-200">
                                    <th colspan="3">Balance:</th>
                                    <td class="text-right font-semibold">{{ model.balance }}
                                    </td>
                                </tr>
                                <tr v-else-if=" model.balance < 0 " class="bg-green-200">
                                    <th colspan="3">OverPayment:</th>
                                    <td class="text-right font-semibold">{{ Math.abs(model.balance) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="row mt-7">
                <div class="col-md-12">
                    <TextareaElement name="notation" label="Notations" id="notation" :debounce=" 500 "
                        rules="required" />
                </div>
            </div>

        </div>
        <div v-else class="no-partner">
            <div class=" text-center border-dashed p-5 rounded border border-red-600">
                <span class="fa-stack text-red-400 " style="vertical-align: top; font-size:36px;">
                    <i class="far fa-circle fa-stack-2x"></i>
                    <i class="fas fa-file-alt fa-stack-1x"></i>
                </span>
                <h2 class="text-red-600">No Partner Selected</h2>
                <p class=" text-red-400">All invoices should have partner selected first. Kindly select the parner.</p>
            </div>
        </div>

    </edit-render>
</template>

<script>
export default {
    data() {
        return {
            id: null,
            timestamp: "",
            setting: {
                partner_id: {
                    path_param: ["partner", "partner"],
                    fields: ['first_name', 'last_name', 'email'],
                    template: '[first_name] [last_name] - [email]',
                },
            },
            invoice: {},
            rates: [],
            ledgers: [],
            partner: {},
            has_partner: false,
            company: {
                name: "Mybizna, Inc.",
                address: "P.O Box 767 - 00618",
                city: "Nairobi",
                country: "Kenya",
                phone: "+254 713 034 569",
                email: "info@mybizna.com",
            },
            model: {
                partner_id: '',
                total: 0.00,
                subtotal: 0.00,
                gateways: [],
                due_date: [],
                items: [{
                    id: "",
                    title: "",
                    ledger_id: "",
                    quantity: 1,
                    price: 0.00,
                    rates: [],
                    rate_ids: [],
                    total: 0.00,
                }],
                title: '',
                rates_used: [],
                paid_amount: 0.00,
                balance: 0.00,
                notation: '',
                status: 'draft',
            },
        };
    },
    computed: {
        getStatusClass() {

            var classes = '';

            switch (this.model.status) {
                case 'paid':
                    classes = 'bg-green-700';
                    break;
                case 'draft':
                    classes = 'bg-gray-500';
                    break;
                case 'partial':
                    classes = 'bg-orange-500';
                    break;
                case 'pending':
                default:
                    classes = 'bg-red-700';
                    break;
            }

            return classes;
        },

    },
    created() {
        var t = this;

        setInterval(function () {
            //alert('sssss');
            t.getNow();
            //alert(t.timestamp);
        }, 1000);
    },
    updated() {
        var t = this;

        this.invoice = {
            id: 'New',
            created_at: '',
        };

        if (Object.prototype.hasOwnProperty.call(t.$router, "params") && Object.prototype.hasOwnProperty.call(t.$router.params, "id")) {
            alert(t.$router.params.id);
        }

        this.fetchData();
    },
    watch: {
        // whenever question changes, this function will run
        'model.partner_id'(newQuestion, oldQuestion) {

            console.log(this.model);

            this.has_partner = true;

            this.fetchData();
        },
    },
    methods: {
        getNow: function () {
            const today = new Date();
            const date = ('0' + today.getDate()).slice(-2) + '/' + ('0' + (today.getMonth() + 1)).slice(-2) + '/' + today.getFullYear();
            const time = ('0' + today.getHours()).slice(-2) + ":" + ('0' + today.getMinutes()).slice(-2) + ":" + ('0' + today.getSeconds()).slice(-2);
            const dateTime = date + ' ' + time;
            this.timestamp = dateTime;
        },
        calculateTotal() {
            var paid_amount = 0.00;

            this.model.gateways.forEach(gateway => {
                console.log(parseFloat(paid_amount));
                console.log(parseFloat(gateway.paid_amount));
                paid_amount = parseFloat(paid_amount) + parseFloat(gateway.paid_amount);
            });

            this.model.paid_amount = paid_amount;

            this.model.balance = this.model.total - this.model.paid_amount;

            if (paid_amount > 0) {
                this.model.status = 'partial';
            } else {
                this.model.status = 'draft';
            }

            if (this.model.balance <= 0) {
                this.model.status = 'paid';
            }

        },

        fetchData() {

            var comp_url = 'invoice/fetchdata/';

            const getdata = async (t) => {

                await window.axios.get(comp_url, { params: { partner_id: this.model.partner_id } })
                    .then(
                        response => {

                            t.model.gateways = response.data.gateways;
                            t.rates = response.data.rates;
                            t.ledgers = response.data.ledgers;
                            t.partner = response.data.partner;

                            t.model.gateways.forEach(gateway => {
                                gateway.reference = '';
                                gateway.others = '';
                                gateway.paid_amount = 0.00;
                            });

                            t.rates.sort(function (a, b) { return a.ordering - b.ordering; });
                        });
            };

            getdata(this);
        },

        addRow() {

            this.model.items.push({
                id: "",
                title: "",
                ledger_id: "",
                quantity: 1,
                price: 0.00,
                rates: [],
                rate_ids: [],
                total: 0.00,
            });

            this.addCalculate();

        },
        addRate(r_index, item, rate) {
            window.$Modal.getOrCreateInstance(document.getElementById('Modal' + r_index)).hide()

            item.rates.push(rate);
            item.rate_ids.push(rate.id);
            this.model.rates_used.push(rate.id);

            item.rates.sort(function (a, b) { return a.ordering - b.ordering; });
            this.model.rates_used = [...new Set(this.model.rates_used)];

            this.addCalculate();
        },
        addCalculate() {
            this.model.total = 0;
            this.model.subtotal = 0;

            this.rates.forEach(main_rate => {
                main_rate.total = 0.00;
            });

            this.model.items.forEach(item => {
                item.total = item.quantity * item.price;

                this.model.subtotal = this.model.subtotal + parseFloat(item.total);

                item.rates.forEach(rate => {
                    var new_val = rate.value;
                    var operation = rate.method;

                    if (new_val != 0) {
                        if (operation == '-') {
                            new_val = -1 * new_val;
                        } else if (operation == '-%') {
                            new_val = -1 * item.total * new_val / 100;
                        } else if (operation == '+%') {
                            new_val = item.total * new_val / 100;
                        }
                    }

                    this.rates.forEach(main_rate => {
                        //main_rate.total = 0.00;
                        if (main_rate.id === rate.id) {
                            console.log(main_rate.id + '' + rate.id);
                            main_rate.total = (Object.prototype.hasOwnProperty.call(main_rate, "total"))
                                ? main_rate.total + new_val
                                : new_val;
                        }
                    });

                    item.total = item.total + new_val;

                });

                this.model.total = this.model.total + parseFloat(item.total);
            });

        }
    }
};
</script>
