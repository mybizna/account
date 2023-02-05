<template>
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
