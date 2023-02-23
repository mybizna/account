<template>
    <table-render :path_param="['account', 'chart_of_account']" title="Chart of Account" :search_fields="search_fields"
        :model="model" :table_fields="table_fields" :setting="{ hide_delete_button: true }">
        
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
            model: {
                name: "",
                slug: "",
            },
            search_fields: [
                { type: "text", name: "name", label: "Name", ope: "", },
                { type: "text", name: "slug", label: "Slug", ope: "", },
            ],
            table_fields: [
                { label: "Name", prop: "name", name: "name", },
                { label: "Slug", prop: "slug", name: "slug", },
            ],
        };
    },
};
</script>
