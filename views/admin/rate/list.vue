<template>
    <table-render :path_param="['account', 'rate']" title="Rate" :table_fields="table_fields">

        <template #header>
            <th-render>Name</th-render>
            <th-render>Slug</th-render>
            <th-render>Value</th-render>
            <th-render>Start Amount</th-render>
            <th-render>End Amount</th-render>
            <th-render>Is Visible</th-render>
            <th-render>Is Percent</th-render>
            <th-render>Published</th-render>
        </template>

        <template #body="{ item }">
            <td>{{ item.title }}</td>
            <td>{{ item.slug }}</td>
            <td>{{ item.value }}</td>
            <td>{{ item.start_amount }}</td>
            <td>{{ item.end_amount }}</td>
            <td class="text-center">
                <btn-status :status="item.is_visible"></btn-status>
            </td>
            <td class="text-center">
                <btn-status :status="item.is_percent"></btn-status>
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
                'title', 'slug', 'value', 'start_amount', 'end_amount',
                'is_visible', 'is_percent', 'published'
            ],
        };
    },
};
</script>
