<template>
    <table-render :path_param="['account', 'coupon']" title="Coupon" :table_fields="table_fields">

        <template #header>
            <th-render>Code</th-render>
            <th-render>Description</th-render>
            <th-render>Value</th-render>
            <th-render>Start Amount</th-render>
            <th-render>End Amount</th-render>
            <th-render>Start Date</th-render>
            <th-render>End Date</th-render>
            <th-render>Applied</th-render>
            <th-render>Is Percent</th-render>
            <th-render>Published</th-render>
            <th-render>Is Visible</th-render>
        </template>
        <template #body="{ item }">
            <td>{{ item.code }}</td>
            <td>{{ item.description }}</td>
            <td>{{ item.value }}</td>
            <td>{{ item.start_amount }}</td>
            <td>{{ item.end_amount }}</td>
            <td>{{ item.start_date }}</td>
            <td>{{ item.end_date }}</td>
            <td>{{ item.applied }}</td>
            <td class="text-center">
                <btn-status :status="item.applied"></btn-status>
            </td>
            <td class="text-center">
                <btn-status :status="item.is_percent"></btn-status>
            </td>
            <td class="text-center">
                <btn-status :status="item.published"></btn-status>
            </td>
            <td class="text-center">
                <btn-status :status="item.is_visible"></btn-status>
            </td>
        </template>

    </table-render>
</template>

<script>
export default {
    data() {
        return {
            table_fields: [
                "code", "description", "value", 'start_amount', 'end_amount',
                'start_date', 'end_date', 'applied', 'is_percent', 'published', 'is_visible'
            ]

        };
    },
};
</script>
