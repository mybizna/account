<template>
    <table-list :path_param="['account', 'invoice']" title="Invoice" :search_fields="search_fields" :model="model"
        :table_fields="table_fields" :setting="{ hide_delete_button: true }"></table-list>
</template>

<script>
export default {
    components: {
        TableList: window.$func.fetchComponent(
            "components/common/TableList.vue"
        ),
    },
    data() {
        return {
            model: {
                title: "",
                partner_id: "",
                invoice_no: "",
                status: "",
                is_posted: "",
                total: "",
            },
            search_fields: [
                { type: "text", name: "title", label: "title", ope: "", },
            ],
            table_fields: [
                { label: "Title", prop: "title", name: "title", },
                { label: "invoice_no", prop: "invoice_no", name: "invoice_no", },
                {
                    label: "Partner",
                    prop: "[partner__first_name] [partner__last_name]",
                    name: "partner_id",
                    foreign: ['partner__first_name', 'partner__last_name'],
                },
                { label: "status", prop: "status", name: "status", },
                { label: "is_posted", prop: "is_posted", name: "is_posted", is_boolean: true, },
                { label: "total", prop: "total", name: "total", },
            ],
        };
    },
};
</script>