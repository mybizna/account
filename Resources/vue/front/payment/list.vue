<template>
    <h1 class="my-3 text-3xl font-bold">Payment</h1>

    <table-render :path_param="['account', 'payment']" title="Payment"  :table_fields="table_fields"
        :setting="{ hide_delete_button: true }">

        <template #header>
            <th-render>Name</th-render>
            <th-render>Code</th-render>
            <th-render>Code3</th-render>
            <th-render>Latitude</th-render>
            <th-render>Longitude</th-render>
        </template>

        <template #body="{ item }">
            <td>{{ item.name }}</td>
            <td>{{ item.code }}</td>
            <td>{{ item.code3 }}</td>
            <td>{{ item.latitude }}</td>
            <td>{{ item.longitude }}</td>
        </template>

    </table-render>
</template>

<script>
export default {

    data() {
        return {
            table_fields: [
                'title', 'amount', 'ledger_id', 'partner_id', 'gateway_id', 'receipt_no',
                'code', 'others', 'stage', 'status', "type", 'is_posted',
            ],
        };
    },
};
</script>