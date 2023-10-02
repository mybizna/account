<template>
    <table-render :path_param="['account', 'invoice']" title="Invoice" :model="model" :table_fields="table_fields"
        :setting="{ hide_delete_button: true }">

        <template #header>
            <th-render v-for="column in columns" :key="column.name">
                {{ column.label }}
            </th-render>
        </template>



    </table-render>
</template>

<script>
export default {
    created() {
        this.table_fields.push(column.name);
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

            table_fields: [
                "title", "invoice_no", "partner_id", "status", "is_posted", "total",
            ],

            columns: [
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