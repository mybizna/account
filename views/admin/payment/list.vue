<template>
    <table-render :path_param="['account', 'payment']" title="Payment" :table_fields="table_fields"
        :setting="{ hide_delete_button: true }">

        <template #header>
            <th-render>Title</th-render>
            <th-render>Partner</th-render>
            <th-render>Receipt</th-render>
            <th-render>Code</th-render>
            <th-render>Gateway</th-render>
            <th-render>Ledger</th-render>
            <th-render>Status</th-render>
            <th-render>Type</th-render>
            <th-render>Amount</th-render>
        </template>

        <template #body="{ item }">
            <td>{{ item.title }}</td>
            <td>{{ item.partner_id__partner__first_name }}</td>
            <td>{{ item.receipt_no }}</td>
            <td>{{ item.code }}</td>
            <td>{{ item.gateway_id__account_gateway__title }}</td>
            <td>{{ item.ledger_id__account_ledger__name }}</td>
            <td>{{ item.status }}</td>
            <td>{{ item.type }}</td>
            <td>{{ item.amount }}</td>
        </template>

    </table-render>
</template>

<script>
export default {

    data() {
        return {
            table_fields: [
                'title', 'receipt_no', 'code', 'status', 'type', 'amount',
                'partner_id__partner__first_name', 'partner_id__partner__last_name',
                'gateway_id__account_gateway__title', 'ledger_id__account_ledger__name',
            ],
        };
    },
};
</script>
