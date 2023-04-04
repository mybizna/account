<template>
    <edit-render :path_param="['account', 'invoice']" :model="model" passed_form_url="invoice/savedata">

        <div class="relative invoice-form p-1 border border-dotted border-dashed border-green-600 rounded overflow-hidden">
            
            <div style="margin-right: -45px; !important" 
                class="absolute w-48  p-1 top-7 right-0 rotate-45"
                :class="getStatusClass">
                <h3 class="text-center p-1 uppercase font-semibold text-white  text-xl border-b border-t border-dashed border-gray-50"> {{ model.status }} </h3>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <span class="underline">From</span>
                    <address>
                        <template v-if="company.name">
                            <strong>{{ company.name }} </strong><br>
                        </template>
                        <template v-if="company.address || company.postal_code">
                            {{ company.address }} {{ company.postal_code }}<br>
                        </template>
                        <template v-if="company.city || company.country">
                            {{ company.city }}, {{ company.country }}<br>
                        </template>
                        <template v-if="company.phone || company.mobile">
                            Phone: {{ company.phone }} &nbsp; {{ company.mobile }}<br>
                        </template>
                        <template v-if="company.email">
                            Email: {{ company.email }}
                        </template>
                    </address>
                </div>
                <div class="col-md-4">
                    <span class="underline">To</span>
                    <address v-if="partner">
                        <template v-if="partner.first_name || partner.last_name">
                            <strong>{{ partner.first_name }} {{ partner.last_name }}</strong><br>
                        </template>
                        <template v-if="partner.company">
                            <strong>{{ partner.company }} </strong><br>
                        </template>
                        <template v-if="partner.address || partner.postal_code">
                            {{ partner.address }} {{ partner.postal_code }}<br>
                        </template>
                        <template v-if="partner.city || partner.country">
                            {{ partner.city }}, {{ partner.country }}<br>
                        </template>
                        <template v-if="partner.phone || partner.mobile">
                            Phone: {{ partner.phone }} &nbsp; {{ partner.mobile }}<br>
                        </template>
                        <template v-if="partner.email">
                            Email: {{ partner.email }}
                        </template>
                    </address>

                </div>
                <div class="col-md-4">

                    <b>ID:</b> {{ invoice.id }}
                    <br>
                    <b>No:</b> {{ invoice.invoice_no }}
                    <br>
                    <br>
                    <b>Payment Due:</b>

                    <template v-if="invoice.due_date">{{ invoice.due_date }}</template>
                    <template v-else>{{ timestamp }}</template>

                    <br>
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
                    <tr v-for="( item, index) in invoice.items" :key="index">
                        <td>
                            {{ item.title }}
                        </td>
                        <td>
                            {{ item.ledger_id }}
                        </td>
                        <td class="w-28">
                            {{ item.quantity }}
                        </td>
                        <td>
                            {{ item.price }}
                        </td>
                        <td>
                            <span v-for="( item_rate, rate_index) in item.rates" :key="rate_index"
                                class="badge bg-secondary mr-1">
                                {{ item_rate.title }}
                                ({{ item_rate.value }}<span v-if="item_rate.is_percent">%</span>)
                            </span>
                        </td>
                        <td class="font-semibold fs-16 text-right">
                            {{ item.amount }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="row mt-3">
                <div class="col-6">
                    <p class="lead">Payment Methods:</p>

                    <div>
                        <ul class="nav nav-tabs" id="myPayment" role="tablist">
                            <li v-for="(gateway, g_index) in model.gateways" v-bind:key="g_index" class="nav-item"
                                role="presentation">
                                <button :class="!g_index ? 'nav-link active' : 'nav-link'" :id="gateway.slug + '-tab'"
                                    data-bs-toggle="tab" :data-bs-target="'#' + gateway.slug" type="button" role="tab"
                                    :aria-controls="gateway.slug" :aria-selected="!g_index ? 'true' : 'false'">
                                    <i v-if="gateway.paid_amount > 0" class="fas fa-check-circle"></i>
                                    {{
                                        gateway.title
                                    }}</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myPaymentContent">
                            <div v-for="(gateway, g_index) in model.gateways" v-bind:key="g_index"
                                :class="!g_index ? 'tab-pane fade show active' : 'tab-pane fade'" :id="gateway.slug"
                                role="tabpanel" :aria-labelledby="gateway.slug + '-tab'">
                                <div class="p-2">
                                    <FormKit label="Amount" id="amount" type="number" validation="required"
                                        v-model="gateway.paid_amount" @keyup="calculateTotal" />
                                    <template v-if="gateway.slug != 'cash'">
                                        <FormKit label="Reference" id="reference" type="text" v-model="gateway.reference" />
                                        <FormKit label="Others" id="others" type="text" v-model="gateway.others" />
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
                                <template v-for="( rate, index) in rates" :key="index">
                                    <tr v-if="rate.total > 0 || rate.total < 0">
                                        <th>{{ rate.title }} (<span
                                                v-if="rate.method == '-' || rate.method == '-%'">-</span>{{ rate.value
                                                }}<span v-if="rate.method == '-%' || rate.method == '+%'">%</span>)
                                        </th>
                                        <td class="text-right font-semibold">{{ this.$func.money(rate.total) }}</td>
                                    </tr>
                                </template>
                                <tr>
                                    <th>Total:</th>
                                    <td class="text-right font-semibold">{{ invoice.total }}</td>
                                </tr>

                            </tbody>
                        </table>

                        <table class="table">
                            <tbody>
                                <tr>
                                    <th colspan="2">Payments:</th>
                                </tr>
                                <template v-for="(gateway, g_index) in model.gateways" v-bind:key="g_index">
                                    <tr v-if="gateway.paid_amount > 0">
                                        <th>{{ gateway.title }} on Now:</th>
                                        <td class="text-right font-semibold">{{ this.$func.money(gateway.paid_amount) }}
                                            {{ gateway.paid_amount }}
                                        </td>
                                    </tr>
                                </template>
                                <tr v-if="model.balance > 0" class="bg-red-200">
                                    <th>Balance:</th>
                                    <td class="text-right font-semibold">{{ model.balance }}
                                    </td>
                                </tr>
                                <tr v-else-if="model.balance < 0" class="bg-green-200">
                                    <th>OverPayment:</th>
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
                    <FormKit label="Notations" id="description" type="textarea" validation="required"
                        v-model="model.notation" />
                </div>
            </div>

        </div>


    </edit-render>
</template>


<script>
export default {
    data() {
        return {
            id: null,
            invoice: {},
            rates: [],
            ledgers: [],
            partner: {},
            company: {
                name: "Mybizna, Inc.",
                address: "P.O Box 767 - 00618",
                city: "Nairobi",
                country: "Kenya",
                phone: "+254 713 034 569",
                email: "info@mybizna.com",
            },
            model: {
                id: null,
                partner_id: '',
                total: 0.00,
                subtotal: 0.00,
                gateways: [],
                title: '',
                paid_amount: 0.00,
                balance: 0.00,
                notation: '',
                status: 'draft',
            },
        }
    },
    created() {
        this.id = this.$route.params.id;
        this.id = this.model.id;
        this.fetchData();
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
    methods: {

        fetchData() {


            var comp_url = 'invoice/fetchdata/';

            const getdata = async (t) => {
                var id = t.$route.params.id;

                await window.axios.get(comp_url, { params: { id: id } })
                    .then(
                        response => {

                            t.model.gateways = response.data.gateways;
                            t.rates = response.data.rates;
                            t.model.status = 'paid';
                            t.has_partner = true;
                            t.ledgers = response.data.ledgers;
                            t.partner = response.data.partner;
                            t.invoice = response.data.invoice;
                            //t.modal.partner_id = t.partner.id;

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
    }
}
</script>
