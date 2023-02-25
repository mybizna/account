<template>
    <table-render :path_param="['account', 'ledger']" title="Account Ledger" :table_fields="table_fields">
    
        <template #header>
            <th-render>Name</th-render>
            <th-render>Slug</th-render>
            <th-render>Code</th-render>
            <th-render>Chart of Account</th-render>
            <th-render>Category</th-render>
            <th-render>Unused</th-render>
            <th-render>System</th-render>
        </template>
        
        <template #body="{ item }">
            <td>{{ item.name }}</td>
            <td>{{ item.slug }}</td>
            <td>{{ item.code }}</td>
            <td>{{ item.chart_id__account_chart_of_account__name }}</td>
            <td>{{ item.category_id__account_ledger_category__name }}</td>
            <td>{{ item.unused }}</td>
            <td>{{ item.is_system }}</td>
        </template>

    </table-render>
</template>

<script>
export default {
    data() {
        return {
            table_fields: [
                'name','slug','code','unused','is_system',
            'chart_id__account_ledger_category__name','category_id__account_ledger_category__name'
            ],
          
        };
    },
};
</script>
