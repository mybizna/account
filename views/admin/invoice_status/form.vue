<template>
    <edit-render :path_param="path_param" :model="model">

        <div class="row">
            <div class="col-md-6">

                <FormKit v-model="model.id" label="Id" id="id" type="hidden" validation="required" />
                <FormKit v-model="model.type_name" label="Type Name" id="type_name" type="text" validation="required" />
                <FormKit v-model="model.slug" label="Slug" id="slug" type="text" validation="required" />

            </div>
            <div class="col-md-6">

            </div>
        </div>


    </edit-render>
</template>

<script>
export default {
    data () {
        return {
            id: null,
            path_param: ["account", "invoice_status"],
            model: {
                id: "",
                type_name: "",
                slug: "",
            },

        };
    }
};
</script>
