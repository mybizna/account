<template>
    <table-edit :path_param="path_param" :model="model">

        <FormKit button_label="Select Customer" id="partner_id" type="recordpicker"
            comp_url="partner/admin/partner/list.vue" :setting="setting.partner_id" v-model="model.partner_id"
            validation="required" />

        <div class="row">
            <div class="col-md-4">
                <span class="underline">From</span>
                <address>
                    <strong>Mybizna, Inc.</strong><br>
                    P.O Box 767 - 00618<br>
                    Nairobi, Kenya<br>
                    Phone: +254 713 034 569<br>
                    Email: info@mybizna.com
                </address>
            </div>
            <div class="col-md-4">
                <span class="underline">To</span>
                <address>
                    <strong>{{ partner.first_name }} {{ partner.first_name }}</strong><br>
                    <strong>{{ partner.company }} </strong><br>
                    {{ partner.address }} {{ partner.postal_code }}<br>
                    {{ partner.city }}, {{ partner.country }}<br>
                    Phone: {{ partner.phone }} &nbsp; {{ partner.mobile }}<br>
                    Email: {{ partner.email }}
                </address>

            </div>
            <div class="col-md-4">
                <b v-if="partner.date_created">Invoice #{{ partner.date_created }}</b>
                <b v-else>Invoice #NEW</b>
                <br>
                <br>
                <b>Payment Due:</b> {{ timestamp }}<br>
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
                <tr v-for="( item, index) in model.items" :key="index">
                    <td>
                        <FormKit id="title" type="text" v-model="item.title" validation="required" />
                    </td>
                    <td>
                        <FormKit id="lerger" type="select" v-model="item.lerger" :options="ledgers"
                            validation="required" />
                    </td>
                    <td class="w-28">
                        <FormKit id="quantity" type="number" v-model="item.quantity" @blur="addCalculate(rate)"
                            validation="required" min="0" />
                    </td>
                    <td>
                        <FormKit id="price" type="number" v-model="item.price" @blur="addCalculate(rate)"
                            validation="required" min="0" step="0.01" />
                    </td>
                    <td>
                        <span v-for="( item_rate, rate_index) in item.rates" :key="rate_index"
                            class="badge bg-secondary mr-1">{{ item_rate.title }} ({{ item_rate.value }}<span
                                v-if="item_rate.is_percent">%</span>)</span>
                        <a class="badge bg-blue-700 text-white cursor-pointer" data-bs-toggle="modal"
                            :data-bs-target="'#' + 'Modal' + index">
                            <i class="fa-solid fa-plus"></i> Add Rate
                        </a>

                        <div class="modal fade" :id="'Modal' + index" tabindex="-1"
                            :aria-labelledby="index + 'ModalLabel'" aria-hidden="true">
                            <div class="modal-dialog ">
                                <div class="modal-content shadow-2xl shadow-indigo-500/50">
                                    <div class="modal-header p-2">
                                        <h5 class="modal-title font-semibold" :id="index + 'ModalLabel'">Select
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
                                                <tr v-for="( rate, r_index) in rates" :key="r_index">
                                                    <td>
                                                        <a v-if="item.rate_ids.includes(rate.id)"
                                                            class="btn btn-danger btn-sm">Remove</a>
                                                        <a v-else class="btn btn-primary btn-sm"
                                                            @click="addRate(index, item, rate)">Add</a>
                                                    </td>
                                                    <td>
                                                        {{ rate.title }}
                                                    </td>
                                                    <td>
                                                        {{ rate.value }} <span v-if="rate.is_percent">%</span>
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

                <FormKit label="Methods" id="methods" type="select" validation="required" />
                <div class="mb-1"></div>
                <FormKit label="Amount" id="methods" type="number" validation="required" />

                <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                    Payment instructions goes here.
                </p>
            </div>

            <div class="col-6">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width:60%">Subtotal:</th>
                                <td class="text-right font-semibold">{{ this.$func.money(model.subtotal) }}</td>
                            </tr>
                            <tr v-for="( rate, index) in rates" :key="index">
                                <th>{{ rate.title }} ({{ rate.value }}<span v-if="rate.is_percent">%</span>)</th>
                                <td class="text-right font-semibold">{{ this.$func.money(rate.total) }}</td>
                            </tr>
                            <tr>
                                <th>Total:</th>
                                <td class="text-right font-semibold">{{ this.$func.money(model.total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mt-7">
            <div class="col-md-12">
                <FormKit label="Notations" id="description" type="textarea" validation="required" />
            </div>
        </div>

    </table-edit>
</template>

<script>
export default {
    components: {
        TableEdit: window.$func.fetchComponent("components/common/TableEdit.vue")
    },
    data () {
        return {
            id: null,
            timestamp: "",
            path_param: ["account", "invoice"],
            setting: {
                partner_id: {
                    path_param: ["partner", "partner"],
                    fields: ['first_name', 'last_name', 'email'],
                    template: '[first_name] [last_name] - [email]',
                },
            },
            invoice: {},
            ledgers: [],
            partner: {},
            rates: [],
            model: {
                total: 0.00,
                subtotal: 0.00,
                items: [{
                    id: "",
                    title: "",
                    lerger: "",
                    quantity: 1,
                    price: 0.00,
                    rates: [],
                    rate_ids: [],
                    total: 0.00,
                }],
            },
        };
    },
    created () {
        var comp_url = 'invoice/fetchdata';

        setInterval(this.getNow, 1000);

        const getdata = async (t) => {

            await window.axios.get(comp_url)
                .then(
                    response => {
                        t.rates = response.data.records;
                        t.ledgers = response.data.ledgers;
                        t.partner = response.data.partner;
                    });
        };

        getdata(this);

    },
    methods: {
        getNow: function () {
            const today = new Date();
            const date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
            const time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
            const dateTime = date + ' ' + time;
            this.timestamp = dateTime;
        },
        addRow () {

            this.model.items.push({
                id: "",
                title: "",
                lerger: "",
                quantity: 1,
                price: 0.00,
                rates: [],
                rate_ids: [],
                total: 0.00,
            });

            this.addCalculate();

        },
        addRate (r_index, item, rate) {
            window.$Modal.getOrCreateInstance(document.getElementById('Modal' + r_index)).hide()

            item.rates.push(rate);
            item.rate_ids.push(rate.id);

            this.addCalculate();
        },
        addCalculate () {
            this.model.total = 0;
            this.model.subtotal = 0;

            this.model.items.forEach(item => {
                item.total = item.quantity * item.price;

                this.model.subtotal = this.model.subtotal + parseFloat(item.total);

                item.rates.forEach(rate => {
                    var new_val = rate.value;
                    var operation = '+';

                    if (rate.is_percent) {
                        new_val = item.total * rate.value / 100;
                    }

                    this.rates.forEach(main_rate => {
                        if (main_rate.id == rate.id) {
                            main_rate.total = (Object.prototype.hasOwnProperty.call(main_rate, "total"))
                                ? main_rate.total + new_val
                                : new_val;
                        }
                    });

                    item.total = (operation == '-') ? item.total - new_val : item.total + new_val;

                });

                this.model.total = this.model.total + parseFloat(item.total);
            });

        }
    }
};
</script>
