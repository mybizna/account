<template>
    <table-render :path_param="['account', 'chart_of_account']" title="Chart of Account" :table_fields="table_fields"
        :setting="{ hide_delete_button: true }">

        <template #header>
            <th-render>Name</th-render>
            <th-render>Slug</th-render>
        </template>

        <template #body="{ item }">
            <td>{{ item.title }}</td>
            <td>{{ item.slug }}</td>
        </template>

    </table-render>
</template>

<script>
export default {
    data() {
        return {
            table_fields:['name','slug']
        };
    },
};
</script>
