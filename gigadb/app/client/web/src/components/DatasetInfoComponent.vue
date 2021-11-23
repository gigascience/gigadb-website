<template>
    <h4>
        GigaDB: {{ stage }} for the dataset {{ identifier }}
    </h4>
</template>
<style>
</style>
<script>
export default {
	props: ['identifier'],
    data: function() {
        return {
            stage: "Uploading files",
        }
    },
}
</script>