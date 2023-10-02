<template>
    <table-render :path_param="['account', 'payment']" title="Payment" :model="model" :table_fields="table_fields"
        :setting="{ hide_delete_button: true }">

        <template #header>
            <th-render v-for="column in columns" :key="column.name">
                {{ column.label }}
            </th-render>
        </template>


    </table-render>
</template>

<script>
export default {

    data() {
        return {
            model: {
                title: "",
                amount: "",
                ledger_id: "",
                partner_id: "",
                gateway_id: "",
                receipt_no: "",
                code: "",
                status: "",
                type: "",
                is_posted: "",
            },
            table_fields: [
                "title", "invoice_no", "partner_id", "receipt_no", "code", "gateway_id", "ledger_id", "status", "type", "amount"
            ],
            table_fields: [
                { text: "title", prop: "title", name: "title", },
                {
                    text: "Partner",
                    prop: "[partner__first_name] [partner__last_name]",
                    name: "partner_id",
                    foreign: ['partner__first_name', 'partner__last_name'],
                },

                { text: "Receipt", prop: "receipt_no", name: "receipt_no", },
                { text: "Code", prop: "code", name: "code", },
                {
                    text: "Gateway",
                    prop: "[account_gateway__title]",
                    name: "gateway_id",
                    foreign: ['account_gateway__title'],
                },
                {
                    text: "Ledger",
                    prop: "[account_ledger__name]",
                    name: "ledger_id",
                    foreign: ['account_ledger__name'],
                },
                { text: "Status", prop: "status", name: "status", },
                { text: "Type", prop: "type", name: "type", },
                { text: "Amount", prop: "amount", name: "amount", },
            ],
        };
    },
};
</script>