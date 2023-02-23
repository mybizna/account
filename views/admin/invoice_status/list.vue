<template>
    <table-render :path_param="['account', 'invoice_status']" title="Invoice Status" :table_fields="table_fields"
        :setting="{ hide_delete_button: true }">

        <template #header>
            <th-render>Type Name</th-render>
            <th-render>Slug</th-render>
        </template>

        <template #body="{ item }">
            <td>{{ item.type_name }}</td>
            <td>{{ item.slug }}</td>
        </template>

    </table-render>
</template>

<script>
export default {
    data() {
        return {
            table_fields: ['type_name', 'slug'],
        };
    },
};
</script>
