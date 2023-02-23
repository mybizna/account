<template>
    <table-render :path_param="['account', 'ledger_category']" title="Account Ledger Category"
        :search_fields="search_fields" :model="model" :table_fields="table_fields"
        :setting="{ hide_delete_button: true }"></table-render>
</template>

<script>
export default {


    data() {
        return {
            model: {
                name: "",
                slug: "",
                chart_id: "",
                parent_id: "",
                system: "",
            },
            search_fields: [
                { type: "text", name: "name", label: "Name", ope: "", },
                { type: "text", name: "slug", label: "Slug", ope: "", },
                { type: "text", name: "chart_id", label: "Chart Id", ope: "", },
                { type: "text", name: "parent_id", label: "Parent", ope: "", },
                { type: "switch", name: "system", label: "System", ope: "", },
            ],
            table_fields: [
                { text: "Name", prop: "name", name: "name", },
                { text: "Slug", prop: "description", name: "description", },
                {
                    text: "Chart of Account",
                    prop: "[account_chart_of_account__name]",
                    name: "chart_id",
                    foreign: ['account_chart_of_account__name'],
                },
                {
                    text: "Parent",
                    prop: "[account_ledger__name]",
                    name: "parent_id",
                    foreign: ['account_ledger__name'],
                },
                { text: "System", prop: "system", align: "center", is_boolean: true, name: "system", },
            ],
        };
    },
};
</script>
