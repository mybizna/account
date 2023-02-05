<template>
    <table-list  module="account" table="chart_of_account"  title="Chart of Account" :search_fields="search_fields" :model="model"
        :table_fields="table_fields"></table-list>
</template>

<script>
export default {
    components: {
        TableList: window.$func.fetchComponent(
            "components/common/TableList.vue"
        ),
    },


    data () {
        return {
            model: {
                name: "",
                slug: "",
            },
            search_fields: [
                { type: "text", name: "name", label: "Name", ope: "", },
                { type: "text", name: "slug", label: "Slug", ope: "", },
                { type: "text", name: "slug", label: "Slugs", ope: "", },
            ],
            table_fields: [
                { label: "Name", prop: "name", name: "name", },
                { label: "Slug", prop: "slug", name: "slug", },
            ],
        };
    },
};
</script>
