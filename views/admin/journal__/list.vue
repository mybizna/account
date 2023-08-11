<template>
    <table-render :path_param="['account', 'journal']" title="Account Journal" :table_fields="table_fields">

        <template #header>
            <th-render>Title</th-render>
            <th-render>Debit</th-render>
            <th-render>Credit</th-render>
        </template>

        <template #body="{ item }">
            <td>{{ item.title }}</td>
            <td>{{ item.debit }}</td>
            <td>{{ item.credit }}</td>
        </template>
    </table-render>
</template>

<script>
export default {
    data() {
        return {
            table_fields: ['title', 'debit', 'credit'],
        };
    },
};
</script>
