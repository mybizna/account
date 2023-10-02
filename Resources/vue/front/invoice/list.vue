<template>
    <h1 class="my-3 text-3xl font-bold">Invoice</h1>

    <table-render :path_param="['account', 'invoice']" title="Invoice" :table_fields="table_fields"
        :setting="{ hide_delete_button: true }">

        <template #header>
            <th-render>Title</th-render>
            <th-render>Invoice No</th-render>
            <th-render>Partner Id</th-render>
            <th-render>Status</th-render>
            <th-render>Is Posted</th-render>
            <th-render>Total</th-render>
            <th-render>Longitude</th-render>
        </template>
        
        <template #body="{ item }">
            <td>{{ item.title }}</td>
            <td>{{ item.invoice_no }}</td>
            <td>{{ item.partner_id }}</td>
            <td>{{ item.status }}</td>
            <td>{{ item.is_posted }}</td>
            <td>{{ item.total }}</td>
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
            table_fields: [
                "title", "invoice_no", "partner_id", "status", "is_posted", "total",
            ],
        };
    },
};
</script>