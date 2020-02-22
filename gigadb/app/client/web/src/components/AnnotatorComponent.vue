<template>
    <div>
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
                            <select v-model="upload.datatype" v-bind:name="'Upload['+ upload.id +'][datatype]'" v-bind:id="'upload-'+(index+1)+'-datatype'" v-on:change="fieldHasChanged(index, $event)">
                                <option v-for="datatype in dataTypes">{{datatype}}</option>
                            </select>
                        </div>
                    </td>
                    <td>{{ upload.extension }}</td>
                    <td>{{ upload.size }}</td>
                    <td>
                        <div class="form-group required">
                            <label class='control-label'>
                                <input v-model="upload.description" type="text" v-bind:name="'Upload['+ upload.id +'][description]'" v-bind:id="'upload-'+(index+1)+'-description'" v-on:input="fieldHasChanged(index, $event)" required>
                            </label>
                        </div>
                    </td>
                    <td>
                        <el-button v-bind:id="'upload-'+(index+1)+'-tag'" v-on:click="toggleDrawer(index, upload.id)" type="primary" class="btn btn-info btn-small">
                            Attributes
                        </el-button>
                        <el-button v-bind:class="'delete-button-'+index" type="danger" icon="el-icon-delete" v-on:click="deleteUpload(index, upload.id)" circle></el-button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-if="uploadedFiles.length > 0">
            <el-drawer v-bind:title="'Add attributes to file: '+uploadedFiles[drawerIndex].name" v-bind:visible.sync="drawer" v-bind:with-header="true" ref="drawer">
                <span>
                    <specifier id="attributes-form" v-bind:fileAttributes="fileAttributes[selectedUpload]" />
                </span>
            </el-drawer>
        </div>
        <input v-for="(uploadId, index) in filesToDelete" type="hidden" v-bind:name="'DeleteList['+index+']'" v-bind:value="uploadId" />
    </div>
</template>
<style>
.form-group.required .control-label:after {
    content: "*";
    color: red;
}
</style>
<script>
import { eventBus } from '../index.js'
import SpecifierComponent from './SpecifierComponent.vue'

export default {
    props: ['identifier', 'token', 'uploads', 'attributes'],
    data: function() {
        return {
            uploadedFiles: this.uploads || [],
            fileAttributes: this.attributes || [],
            filesToDelete: [],
            metaComplete: [],
            dataTypes: [
                "Text",
                "Image",
                "Rich Text",
                "Genome Sequence",
            ],
            drawer: false,
            drawerIndex: 0,
            selectedUpload: -1,
        }
    },
    methods: {
        fieldHasChanged(uploadIndex, event) {
            if (this.uploadedFiles[uploadIndex].datatype != undefined && this.uploadedFiles[uploadIndex].datatype.length > 0 && this.uploadedFiles[uploadIndex].description != undefined && this.uploadedFiles[uploadIndex].description.length > 0) {
                this.metaComplete[uploadIndex] = true
            } else {
                this.metaComplete = this.metaComplete.filter(
                    (x, i) => i !== uploadIndex
                )
                eventBus.$emit('metadata-ready-status', false)
            }

            if (this.isMetadataComplete()) {
                eventBus.$emit('metadata-ready-status', true)
            } else {
                eventBus.$emit('metadata-ready-status', false)
            }
        },
        isMetadataComplete() {
            return this.metaComplete.length === this.uploadedFiles.length
        },
        toggleDrawer(uploadIndex, uploadId) {
            this.drawerIndex = uploadIndex
            this.selectedUpload = uploadId
            this.drawer = !this.drawer
        },
        deleteUpload(uploadIndex, uploadId) {
            this.uploadedFiles.splice(uploadIndex, 1)
            this.filesToDelete.push(uploadId)
        }
    },
    beforeDestroy: function() {
        console.log("before destroy")
        delete this.uploadedfiles
    },
    destroyed: function() {
        console.log("after destroy")
    },
    mounted: function() {
        this.$nextTick(function() {
            eventBus.$emit("stage-changed", "annotating")
        })
    },
    components: {
        "specifier": SpecifierComponent,
    }
}
</script>