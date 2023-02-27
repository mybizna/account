<template>
    <edit-render :path_param="['account', 'ledger_category']" :model="model">

        <div class="row">
            <div class="col-md-6">

                <FormKit v-model="model.id" label="Id" id="id" type="hidden" validation="required" />
                <FormKit v-model="model.name" label="Name" id="name" type="text" validation="required" />
                <FormKit v-model="model.slug" label="Slug" id="slug" type="text" validation="required" />
                <FormKit v-model="model.chart_id" label="Chart" id="chart_id" type="text" validation="required" />
                <FormKit v-model="model.parent_id" label="Parent" id="parent_id" type="text" validation="required" />

            </div>
            <div class="col-md-6">
                <FormKit v-model="model.system" label="System" id="system" type="switch" validation="required" />

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
                name: "",
                slug: "",
                chart_id: "",
                parent_id: "",
                system: "",
            },

        };
    }
};
</script>
