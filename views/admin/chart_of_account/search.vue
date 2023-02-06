<template>
    <FormKit v-model="model.slug" label="Slug" id="slug" type="text" validation="required" />
</template>

<script>

export default {
    watch: {
        model: {
            handler: function (newVal) {
                this.$emitter.emit("system-search", { module: 'account', table: 'chart_of_account', search: this.model });
            },
            deep: true
        },
    },
    data() {
        return {
            id: null,
            model: {
                slug: "",
            },

        };
    },
};
</script>
