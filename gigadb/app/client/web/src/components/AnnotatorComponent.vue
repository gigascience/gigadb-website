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
                    <td><span data-toggle='tooltip' data-placement='bottom' v-bind:title="'md5:'+upload.initial_md5">{{ upload.name }}</span></td>
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
                        <input type="hidden" v-bind:name="'Upload['+ upload.id +'][sample_ids]'" v-bind:id="'upload-'+(index+1)+'-sample_ids'" v-bind:value="upload.sample_ids" >
                        <el-button v-bind:id="'upload-'+(index+1)+'-tag'" v-on:click="toggleAttrDrawer(index, upload.id)" type="primary" v-bind:class="'btn btn-info btn-small attribute-button '+upload.name">
                            Attributes
                        </el-button>
                        <el-button v-bind:id="'upload-'+(index+1)+'-sample'" v-on:click="toggleSampleDrawer(index, upload.id)" type="primary" v-bind:class="'btn btn-info btn-small sample-button '+upload.name">
                            Sample IDs
                        </el-button>                        
                        <el-button v-bind:id="'upload-'+(index+1)+'-delete'" v-bind:class="'delete-button-'+index" type="danger" icon="el-icon-delete" v-on:click="deleteUpload(index, upload.id)" circle></el-button>
                    </td>
                </tr>
            </tbody>
        </table>
        <aside>
            <form id="bulkUploadForm" method="post" enctype="multipart/form-data">
                <p>If you have many files you may wish to prepare the information in a spreadsheet and upload it to this form using the file input below. Note the columns should have a header row. Please check out <a href="/files/examples/bulk-data-upload-example.csv">this example spreadsheet</a> for header names.</p>
                <label for="bulkmetadata">Upload file metadata from spreadsheet:</label>
                <input type="file" id="bulkmetadata" name="bulkmetadata" accept=".csv, .tsv">
                <button class="btn btn-primary btn-small">Upload spreadsheet</button>
            </form>
        </aside>
        <div v-if="uploadedFiles.length > 0">
            <el-drawer v-bind:title="'Add attributes to file: '+uploadedFiles[drawerIndex].name" v-bind:visible.sync="attrPanel" v-bind:with-header="true" ref="attrPanel">
                <span>
                    <specifier id="attributes-form" v-bind:fileAttributes="fileAttributes[selectedUpload]" />
                </span>
            </el-drawer>
            <el-drawer v-bind:title="'Add samples to file: '+uploadedFiles[drawerIndex].name" v-bind:visible.sync="samplePanel" v-bind:with-header="true" ref="samplesPanel">
                <span>
                    <sampler id="samples-form" 
                            v-bind:collection="samplesArray[selectedUpload]" 
                            v-on:new-samples-input="setSampleIds(drawerIndex)"
                    />
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
import SamplerComponent from './SamplerComponent.vue'

export default {
    props: ['identifier', 'token', 'uploads', 'attributes', 'filetypes'],
    data: function() {
        return {
            uploadedFiles: this.uploads || [],
            fileAttributes: this.attributes || [],
            filesToDelete: [],
            samplesArray: [],
            metaComplete: [],
            dataTypes: Object.keys(this.filetypes),
            attrPanel: false,
            samplePanel: false,
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
        toggleAttrDrawer(uploadIndex, uploadId) {
            this.drawerIndex = uploadIndex
            this.selectedUpload = uploadId
            this.attrPanel = !this.attrPanel
        },
        toggleSampleDrawer(uploadIndex, uploadId) {
            this.drawerIndex = uploadIndex
            this.selectedUpload = uploadId
            this.samplePanel = !this.samplePanel
            console.log(`Toogling sample drawer: ${this.samplePanel}`)
        },
        deleteUpload(uploadIndex, uploadId) {
            this.uploadedFiles.splice(uploadIndex, 1)
            this.filesToDelete.push(uploadId)
        },
        setSampleIds(uploadIndex) {
            if(this.samplesArray[this.selectedUpload]) {
                this.uploadedFiles[uploadIndex].sample_ids =  this.samplesArray[this.selectedUpload].join(',')
                console.log(`Assigned sample_ids ${this.uploadedFiles[uploadIndex].sample_ids}`)
            }
            this.toggleSampleDrawer(uploadIndex, this.selectedUpload)
        },
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
        "sampler": SamplerComponent,
    }
}
</script>