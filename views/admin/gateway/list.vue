<template>
    <table-render :path_param="['account', 'gateway']" title="Gateway" :table_fields="table_fields">

        <template #header>
            <th-render>Title</th-render>
            <th-render>Slug</th-render>
            <th-render>Currency</th-render>
            <th-render>Image</th-render>
            <th-render>Url</th-render>
            <th-render>Ordering</th-render>
            <th-render>Default</th-render>
            <th-render>Hidden</th-render>
            <th-render>Published</th-render>
        </template>
        <template #body="{ item }">
            <td>{{ item.title }}</td>
            <td>{{ item.slug }}</td>
            <td>{{ item.currency_id__core_currency__name }}</td>
            <td>{{ item.image }}</td>
            <td>{{ item.url }}</td>
            <td>{{ item.ordering }}</td>
            <td>{{ item.is_default }}</td>
            <td>{{ item.is_hidden }}</td>
            <td>{{ item.published }}</td>
        </template>

    </table-render>
</template>

<script>
export default {
    data() {
        return {
            table_fields: [
                'title', 'slug', 'currency_id', 'currency_id__core_currency__name', 
                'currency_id__core_currency__name',
                'image', 'url', 'ordering', 'is_default', 'is_hidden', 'published'
            ],
        };
    },
};
</script>
