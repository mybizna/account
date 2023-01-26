<template>
    <table-edit :path_param="path_param" :model="model">
        <div class="row">
            <div class="col-md-6">
                <FormKit v-model="model.id" label="Id" id="id" type="hidden" validation="required" />
                <FormKit v-model="model.amount" label="Amount" id="amount" type="text" validation="required" />
                <FormKit v-model="model.ledger_id" label="Ledger" id="ledger_id" type="text" validation="required" />
                <FormKit v-model="model.partner_id" label="Partner" id="partner_id" type="text" validation="required" />
                <FormKit v-model="model.gateway_id" label="Gateway" id="gateway_id" type="text" validation="required" />
                <FormKit v-model="model.receipt_no" label="Receipt No" id="receipt_no" type="text" validation="required" />
                <FormKit v-model="model.code" label="Code" id="code" type="text" validation="required" />
            </div>
            <div class="col-md-6">
                <FormKit v-model="model.status" label="Ctatus" id="status" type="text" validation="required" />
                <FormKit v-model="model.type" label="Type" id="type" type="switch" validation="required" />
                <FormKit v-model="model.is_posted" label="Is Posted" id="is_posted" type="switch" validation="required" />
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
            path_param: ["account", "payment"],
            model: {
                id: "",
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

        };
    }
};
</script>
