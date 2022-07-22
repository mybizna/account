<template>
    <vueform></vueform>
</template>

<script>
export default {
    components: {
        vueform: window.$func.fetchComponent(["account", "transaction", "form.vue"])
    },
};
</script>
