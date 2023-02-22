<template>
    <table-list title="Account Journal" :path_param="path_param" :search_fields="search_fields" :model="model"
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
            path_param: ["account", "journal"],
            model: {
                title: "",
                debit: "",
                credit: "",
                note: "",
                particulars: "",
            },
            search_fields: [
                { type: "text", name: "title", label: "title", ope: "", },
                { type: "text", name: "debit", label: "debit", ope: "", },
                { type: "text", name: "credit", label: "credit", ope: "", },
            ],
            table_fields: [
                { text: "Title", prop: "title", name: "title", },
                { text: "Debit", prop: "debit", name: "debit", },
                { text: "Credit", prop: "credit", name: "credit", },
            ],
        };
    },
};
</script>
