<template>
    <edit-render :path_param=" ['account', 'journal']" :model="model">

        <div class="row">
            <div class="col-md-6">
                <FormKit v-model="model.id" label="Id" id="id" type="hidden" validation="required" />
                <FormKit v-model="model.note" label="Note" id="note" type="text" validation="required" />
                <FormKit v-model="model.transaction_date" label="Transaction Date" id="transaction_date" type="text" validation="required" />
                <FormKit v-model="model.debit" label="Debit" id="debit" type="text" validation="required" />
                <FormKit v-model="model.credit" label="Credit" id="credit" type="text" validation="required" />
            </div>
            <div class="col-md-6">
                <FormKit v-model="model.particulars" label="Particulars" id="particulars" type="text" validation="required" />
            </div>
        </div>


    </edit-render>
</template>

<script>
export default {
    data () {
        return {
            id: null,
            model: {
                id: "",
                transaction_date: "",
                debit: "",
                credit: "",
                note: "",
                particulars: "",
            },

        };
    }
};
</script>
