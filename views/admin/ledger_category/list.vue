<template>
    <table-render :path_param="['account', 'ledger_category']" title="Account Ledger Category" :table_fields="table_fields"
        :setting="{ hide_delete_button: true }">


        <template #header>
            <th-render>Name</th-render>
            <th-render>Slug</th-render>
            <th-render>Chart of Account</th-render>
            <th-render>Parent</th-render>
            <th-render>System</th-render>
        </template>

        <template #body="{ item }">
            <td>{{ item.name }}</td>
            <td>{{ item.slug }}</td>
            <td>{{ item.description }}</td>
            <td>{{ item.chart_id__account_chart_of_account__name }}</td>
            <td>{{ item.parent_id__account_ledger__name }}</td>
            <td>{{ item.system }}</td>
        </template>

    </table-render>
</template>

<script>
export default {


    data() {
        return {
            table_fields: [
                'name', 'description', 'system', 'parent_id__account_ledger__name',
                'chart_id__account_chart_of_account__name'
            ],

        };
    },
};
</script>
