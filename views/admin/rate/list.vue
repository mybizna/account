<template>
    <table-render :path_param="['account', 'rate']" title="Rate" :table_fields="table_fields">

        <template #header>
            <th-render>Tttle</th-render>
            <th-render>Slug</th-render>
            <th-render>Value</th-render>
            <th-render>Ledger</th-render>
            <th-render>Method</th-render>
            <th-render>Ordering</th-render>
            <th-render>On Total</th-render>
            <th-render>Published</th-render>
        </template>


        <template #body="{ item }">
            <td>{{ item.title }}</td>
            <td>{{ item.slug }}</td>
            <td>{{ item.value }}</td>
            <td>{{ item.ledger_id__account_ledger__name }}</td>
            <td>{{ item.method }}</td>
            <td>{{ item.ordering }}</td>
            <td class="text-center">
                <btn-status :status="item.on_total"></btn-status>
            </td>
            <td class="text-center">
                <btn-status :status="item.published"></btn-status>
            </td>
        </template>

    </table-render>
</template>

<script>
export default {
    data() {
        return {
            table_fields: [
                'title', 'slug', 'value', 'ledger_id__account_ledger__name', 'value',
                'method', 'params', 'ordering', 'on_total', 'published',
            ],
        };
    },
};
</script>
