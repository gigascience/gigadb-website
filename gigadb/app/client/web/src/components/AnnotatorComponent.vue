<template>
    <form v-bind:id="'metadata-form-' + identifier">
        <table class="table table-striped table-bordered">
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
                <tr v-for="(upload, index) in uploadedFiles">
                    <td>{{ upload.name }}</td>
                    <td>
                        <div class="form-group">
                            <select v-model="upload.datatype" name="datatype" v-bind:id="'upload-'+(index+1)+'-datatype'">
                                <option v-for="datatype in dataTypes">{{datatype}}</option>
                            </select>
                        </div>
                    </td>
                    <td>{{ upload.extension }}</td>
                    <td>{{ upload.size }}</td>
                    <td>
                        <div class="form-group required">
                            <label class='control-label'>
                                <input v-model="upload.description" type="text" name="description" v-bind:id="'upload-'+(index+1)+'-description'" required>
                            </label>
                        </div>
                    </td>
                    <td><a href="" v-bind:id="'upload-'+(index+1)+'-tag'" class="btn btn-info btn-small">Tag</a><a href="" v-bind:id="'upload-'+(index+1)+'-delete'" class="btn btn-danger btn-small">Del.</a></td>
                </tr>
            </tbody>
        </table>
    </form>
</template>
<style>
.form-group.required .control-label:after {
    content: "*";
    color: red;
}
</style>
<script>
import axios from "axios"

export default {
    props: ['identifier', 'token', 'uploads'],
    data: function() {
        return {
            uploadedFiles: this.uploads || [],
            dataTypes: [
                "Text",
                "Image",
                "Rich Text",
                "Genome Sequence",
            ],
        }
    },
}
</script>