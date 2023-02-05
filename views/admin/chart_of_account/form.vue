<template>
    <table-edit module="account" table="chart_of_account" title="Chart of Account" :model="model">

        <div class="row">
            <div class="col-md-6">

                <FormKit v-model="model.id" label="Id" id="id" type="hidden" validation="required" />
                <FormKit v-model="model.name" label="Name" id="name" type="text" validation="required" />
                <FormKit v-model="model.slug" label="Slug" id="slug" type="text" validation="required" />

            </div>
            <div class="col-md-6">

            </div>
        </div>


    </table-edit>
</template>

<script>
export default {
    components: {
        TableEdit: window.$func.fetchComponent("components/common/TableEdit.vue")
    },

    data() {
        return {
            id: null,
            model: {
                id: "", 
                name: "", 
                slug: "",
            },

        };
    }
};
</script>
