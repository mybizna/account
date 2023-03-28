<template>
    <edit-render :path_param="['account', 'invoice']" :model="model" passed_form_url="invoice/savedata">

        <div class="row mb-2">
            <div class="col-sm-6">
                <FormKit label="Invoice Title" id="title" type="text" v-model="model.title" validation="required"
                    inner-class="$reset formkit-inner" wrapper-class="$reset formkit-wrapper" input-class="h-10" />
            </div>
            <div class="col-sm-6">
                <FormKit label="Select Partner" button_label="Select Partner" id="partner_id" type="recordpicker"
                    comp_url="partner/admin/partner/list.vue" :setting="setting.partner_id" v-model="model.partner_id"
                    validation="required" inner-class="$reset formkit-inner" wrapper-class="$reset formkit-wrapper" />
            </div>

        </div>

        <div v-if="has_partner" class="invoice-form p-1 border border-dotted border-dashed border-green-600 rounded">
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
                    <address v-if="partner">
                        <strong>{{ partner.first_name }} {{ partner.last_name }}</strong><br>
                        <strong>{{ partner.company }} </strong><br>
                        {{ partner.address }} {{ partner.postal_code }}<br>
                        {{ partner.city }}, {{ partner.country }}<br>
                        Phone: {{ partner.phone }} &nbsp; {{ partner.mobile }}<br>
                        Email: {{ partner.email }}
                    </address>

                </div>
                <div class="col-md-4">
                    <div :class="model.status == 'paid' ? 'bg-green' : (model.status == 'draft' ? 'bg-grey' : 'bg-red')">
                        <h3 class="text-center p-2 uppercase font-semibold text-white"> {{ model.status }} </h3>
                    </div>
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
                    <tr v-for="( item, index) in model.items" :key="index">
                        <td>
                            <FormKit id="title" type="text" v-model="item.title" validation="required" />
                        </td>
                        <td>
                            <FormKit id="ledger_id" type="select" v-model="item.ledger_id" :options="ledgers"
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
                                <tr>
                                    <th style="width:60%">Subtotal:</th>
                                    <td class="text-right font-semibold">{{ this.$func.money(model.subtotal) }}</td>
                                </tr>
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
                                    <td class="text-right font-semibold">{{ this.$func.money(model.total) }}</td>
                                </tr>

                            </tbody>
                        </table>

                        <table class="table">
                            <tbody>
                                <tr>
                                    <th colspan="2">Paid:</th>
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
            invoice: {},
            rates: [],
            ledgers: [],
            partner: {},
        }
    },
    created() {
        this.id = this.$route.params.id;

        this.fetchData();
    },
}
</script>
