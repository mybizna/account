<template>
    <table-render :path_param="['account', 'invoice_status']" title="Invoice Status" :search_fields="search_fields"
        :model="model" :table_fields="table_fields" :setting="{ hide_delete_button: true }"></table-render>
</template>

<script>
export default {
    data() {
        return {
            model: {
                type_name: "",
                slug: "",
            },
            search_fields: [
                { type: "text", name: "type_name", label: "Type Name", ope: "", },
                { type: "text", name: "slug", label: "Slug", ope: "", },
            ],
            table_fields: [
                { text: "Type Name", prop: "type_name", name: "type_name", },
                { text: "Slug", prop: "slug", name: "slug", },
            ],
        };
    },
};
</script>
