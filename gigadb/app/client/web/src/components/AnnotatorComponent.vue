<template>
    <div>
        <table>
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Data Type</th>
                    <th>Format</th>
                    <th>Size</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="upload in uploadedFiles">
                    <td>{{ upload.name }}</td>
                    <td>{{ upload.datatype }}</td>
                    <td>{{ upload.extension }}</td>
                    <td>{{ upload.size }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<style></style>
<script>
import axios from "axios"

export default {
    props: ['identifier', 'token', 'uploads'],
    data: function() {
        return {
            uploadedFiles: this.uploads || [],
        }
    },
}
</script>