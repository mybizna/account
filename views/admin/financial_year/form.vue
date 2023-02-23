<template>
    <edit-render :path_param="path_param" :model="model">

        <div class="row">
            <div class="col-md-6">

                <FormKit v-model="model.id" label="Id" id="id" type="hidden" validation="required" />
                <FormKit v-model="model.name" label="Name" id="name" type="text" validation="required" />
                <FormKit v-model="model.start_date" label="Start Date" id="start_date" type="datetime" validation="required" />
                <FormKit v-model="model.end_date" label="End Date" id="end_date" type="datetime" validation="required" />

            </div>
            <div class="col-md-6">
                <FormKit v-model="description" label="Description" id="description" type="textarea" validation="required" />

            </div>
        </div>


    </edit-render>
</template>

<script>
export default {
    data () {
        return {
            id: null,
            path_param: ["account", "financial_year"],
            model: {
                id: "",
                name: "",
                description: "",
                start_date: "",
                end_date: "",
            },

        };
    }
};
</script>
