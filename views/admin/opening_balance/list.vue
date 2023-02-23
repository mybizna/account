<template>
    <table-render :path_param="['account', 'opening_balance']" title="Account Opening Balance" :table_fields="table_fields"
        :setting="{ hide_delete_button: true }">


        <template #header>
            <th-render>Type</th-render>
            <th-render>Financial Year</th-render>
            <th-render>Chart of Account</th-render>
            <th-render>Ledger</th-render>
            <th-render>Debit</th-render>
            <th-render>Credit</th-render>
        </template>

        <template #body="{ item }">
            <td>{{ item.type }}</td>
            <td>{{ item.financial_year_id__account_financial_year__name }}</td>
            <td>{{ item.chart_id__account_chart_of_account__name }}</td>
            <td>{{ item.ledger_id__account_ledger__name }}</td>
            <td>{{ item.debit }}</td>
            <td>{{ item.credit }}</td>
        </template>

    </table-render>
</template>

<script>
export default {
    data() {
        return {
            table_fields: [
                'type', 'chart_id__account_chart_of_account__name', 'debit', 'credit',
                'financial_year_id__account_financial_year__name', 'ledger_id__account_ledger__name'
            ],
        };
    },
};
</script>
