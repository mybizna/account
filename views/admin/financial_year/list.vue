<template>
    <table-render :path_param="['account', 'financial_year']" title="Financial Year" :table_fields="table_fields"
        :setting="{ hide_delete_button: true }">

        <template #header>
            <th-render>Name</th-render>
            <th-render>Description</th-render>
            <th-render>Start Time</th-render>
            <th-render>End Time</th-render>
        </template>
        <template #body="{ item }">
            <td>{{ item.name }}</td>
            <td>{{ item.description }}</td>
            <td>{{ item.start_date }}</td>
            <td>{{ item.end_date }}</td>
        </template>

    </table-render>
</template>

<script>
export default {

    data() {
        return {
            table_fields: ['name', 'description', 'start_date', 'end_date']
        };
    },
};
</script>
