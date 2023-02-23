<template>
    <table-list :path_param="['account', 'payment']" title="Payment" :search_fields="search_fields" :model="model"
        :table_fields="table_fields" :setting="{ hide_delete_button: true }"></table-list>
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

            search_fields: [
                { type: "text", name: "title", label: "title", ope: "", },
                { type: "text", name: "amount", label: "amount", ope: "", },
                { type: "text", name: "receipt_no", label: "receipt_no", ope: "", },
                { type: "text", name: "code", label: "code", ope: "", },
                { type: "text", name: "status", label: "status", ope: "", },
                { type: "text", name: "type", label: "type", ope: "", },
                { type: "text", name: "is_posted", label: "is_posted", ope: "", },
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
