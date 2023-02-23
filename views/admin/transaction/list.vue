<template>
    <table-render :path_param="['account', 'transaction']" title="Transaction" :table_fields="table_fields"
        :setting="{ hide_delete_button: true }">

        <template #header>
            <th-render>Amount</th-render>
            <th-render>Description</th-render>
            <th-render>Partner</th-render>
            <th-render>Left Chart of Account</th-render>
            <th-render>Left Ledger</th-render>
            <th-render>Right Chart of Account</th-render>
            <th-render>Right Ledger</th-render>
            <th-render>Is Processed</th-render>
        </template>

        <template #body="{ item }">
            <td>{{ item.amount }}</td>
            <td>{{ item.description }}</td>
            <td>{{ item.partner_id }}</td>
            <td>{{ item.partner_id__partner__first_name }} {{ item.partner_id__partner__last_name }}</td>
            <td>{{ item.left_chart_of_account_id__account_chart_of_account__name }}</td>
            <td>{{ item.left_ledger_id__account_ledger__name }}</td>
            <td>{{ item.right_chart_of_account_id__account_chart_of_account__name }}</td>
            <td>{{ item.right_ledger_id__account_ledger__name }}</td>
            <td>{{ item.is_processed }}</td>
        </template>

    </table-render>
</template>

<script>
export default {
    data() {
        return {
            table_fields: [
                'amount', 'description', 'partner_id', 'is_processed',
                'partner_id__partner__first_name', 'partner_id__partner__last_name',
                'left_chart_of_account_id__account_chart_of_account__name',
                'right_chart_of_account_id__account_chart_of_account__name',
                'left_ledger_id__account_ledger__name', 'right_ledger_id__account_ledger__name'
            ],
        };
    },
};
</script>
