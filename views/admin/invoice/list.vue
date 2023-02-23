<template>
    <table-render :path_param="['account', 'invoice']" title="Invoice" :table_fields="table_fields"
        :setting="{ hide_delete_button: true }">


        <template #header>
            <th-render>Title</th-render>
            <th-render>Invoice No</th-render>
            <th-render>Partner</th-render>
            <th-render>Status</th-render>
            <th-render>Is Posted</th-render>
            <th-render>Total</th-render>
        </template>

        <template #body="{ item }">
            <td>{{ item.title }}</td>
            <td>{{ item.invoice_no }}</td>
            <td>{{ item.partner_id__partner__first_name }} {{ item.partner_id__partner__last_name }}</td>
            <td>{{ item.status }}</td>
            <td>{{ item.is_posted }}</td>
            <td>{{ item.total }}</td>
        </template>
    </table-render>
</template>

<script>
export default {
    data() {
        return {
            table_fields: [
                "title", "invoice_no", "status", "is_posted", "total",
                "partner_id__partner__first_name", "partner_id__partner__last_name"
            ],

        };
    },
};
</script>
