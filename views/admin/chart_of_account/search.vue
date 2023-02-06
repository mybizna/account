<template>
    <FormKit v-model="model.slug" label="Slug" id="slug" type="text" validation="required" />
</template>

<script>

export default {
    watch: {
        model: {
            handler: function (newVal) {
                this.$store.commit('system/search', { module: 'account', table: 'chart_of_account', search: this.model });
            },
            deep: true
        },
        '$store.state.system.search': {
            handler: function (newVal) {
                console.log('saved_searches');
            },
            deep: true
        }
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
    },
};
</script>
