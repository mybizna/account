<template>
    <FormKit v-model="model.id" label="Id" id="id" type="hidden" validation="required" />
    <FormKit v-model="model.name" label="Name" id="name" type="text" validation="required" />
    <FormKit v-model="model.slug" label="Slug" id="slug" type="text" validation="required" />
</template>

<script>

export default {

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
