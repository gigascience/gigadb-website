<template>
  <div class="container">
    <p>Please, use this table to annotate the files you've just uploaded with metadata. Once you're done with mandatory
      fields (Data Type and Description) for all files, a "Complete and return to Your Uploaded Datasets page" button will
      appear at the bottom of the page. You must click it to effect your file submission.</p>
    <div class="row">
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
          <tr v-for="(upload, index) in uploadedFiles" :key="upload.id">
            <td><span data-toggle="tooltip" data-placement="bottom" :title="`md5:${upload.initial_md5}`">{{
              upload.name }}</span>
              <input type="hidden" :name="`Upload[${upload.id}][name]`" :value="upload.name">
            </td>
            <td>
              <div class="form-group">
                <select v-model="upload.datatype" :name="`Upload[${upload.id}][datatype]`"
                  :id="`upload-${(index + 1)}-datatype`" @change="fieldHasChanged(index, $event)">
                  <option v-for="datatype in dataTypes" :key="datatype">{{ datatype }}</option>
                </select>
              </div>
            </td>
            <td>{{ upload.extension }}</td>
            <td>{{ upload.size }}</td>
            <td>
              <div class="form-group required">
                <label class='control-label'>
                  <input v-model="upload.description" class="form-control" type="text"
                    :name="`Upload[${upload.id}][description]`" :id="`upload-${(index + 1)}-description`"
                    @input="fieldHasChanged(index, $event)" required>
                </label>
              </div>
            </td>
            <td>
              <input type="hidden" :name="`Upload[${upload.id}][sample_ids]`" :id="`upload-${index + 1}-sample_ids`"
                :value="upload.sample_ids">
              <el-button :id="`upload-${index + 1}-tag`" @click="toggleAttrDrawer(index, upload.id)" type="info"
                :class="`btn btn-green btn-small attribute-button ${upload.name}`">
                Attributes
              </el-button>
              <el-button :id="`upload-${index + 1}-sample`" @click="toggleSampleDrawer(index, upload.id)" type="info"
                :class="`btn btn-green btn-small sample-button ${upload.name}`">
                Sample IDs
              </el-button>
              <el-button :id="`upload-${index + 1}-delete`" :class="`delete-button-${index}`" type="danger"
                icon="el-icon-delete" @click="deleteUpload(index, upload.id)" circle></el-button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>


    <aside>
      <p>If you have many files, you may wish to prepare the information in a spreadsheet and upload that using the form
        below. The metadata table above will be overwritten to reflect the content of the spreadsheet.

        The uploader will only parse CSV and TSV files. Do not try to upload in other formats.

        With this method of bulk metadata upload, you can also associate references to existing samples and to up to five
        existing file attributes. (if the sample or the file attribute do not exist in the GigaDB, they are simply
        ignored).</p>
      <div class="row">
        <div class="col-md-8">
          <!-- WARN this form is nested inside another form -->
          <form id="bulkUploadForm" method="post" enctype="multipart/form-data"
            class="form-horizontal well form-bulk-upload">
            <div class="form-group">
              <label for="bulkmetadata">Select a spreadsheet:</label>
              <input type="file" id="bulkmetadata" name="bulkmetadata" accept=".csv, .tsv">
            </div>
            <button class="btn btn-green btn-small" type="submit">Upload metadata from spreadsheet</button>
          </form>
        </div>
        <div class="col-md-4">
          <div class="panel panel-success panel-tips">
            <div class="panel-heading">
              <h4 class="panel-title">Tips</h4>
            </div>
            <div class="panel-body">
              <p> In order for the metadata to be parsed correctly there are a couple of requirements to follow when
                preparing the spreadsheet:
              </p>
              <ul>
                <li> Ensure the first row is a header with the name with the columns (you can copy the text into the
                  spreadsheet):
                  <ul>
                    <li> TSV:
                      <pre>File Name    Data Type   File Format     Description     Sample IDs  Attribute 1     Attribute 2     Attribute 3     Attribute 4     Attribute 5</pre>
                    </li>
                    <li> CSV:
                      <pre>File Name, Data Type, File Format, Description, Sample IDs, Attribute 1, Attribute 2, Attribute 3, Attribute 4, Attribute 5</pre>
                    </li>
                  </ul>
                </li>
                <li> When adding attributes, enter each one with the format "name::value::unit"</li>
                <li> If there is no unit, the last "::"" is still needed: "name::value::"</li>
                <li> After uploading the spreadsheet, you can still tweak the metadata in the table above</li>
              </ul>

              <p>Here is an example of a valid spreadsheet to illustrate these requirements:</p>
              <ul>
                <li><a href="/files/examples/bulk-data-upload-example.csv">bulk-data-upload-example.csv</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </aside>


    <div v-if="uploadedFiles.length > 0">
      <el-drawer :title="`Add attributes to file: ${uploadedFiles[drawerIndex].name}`" :visible.sync="attrPanel"
        :with-header="true" ref="attrPanel" :before-close="handleAttrClose" destroy-on-close>
        <span>
          <attribute-specifier id="attributes-form" :fileAttributes="fileAttributes[selectedUpload]" />
        </span>
        <div class="panel panel-success panel-drawer-tips">
          <div class="panel-heading">
            <h4 class="panel-title">Tips</h4>
          </div>
          <div class="panel-body">
            <ul>
              <li>The name and unit must be already existing in GigaDB.
                If there is a typo or they don't exist, they will just be ignored upon finalising
                the process.</li>

              <li>You can alternate adding and removing any number of file attributes in this panel with editing the
                metadata in the table on the main form. Your selection won't be lost.</li>

              <li>If you leave/reload the web page, the entries made in the panel will be lost.</li>
            </ul>
          </div>
        </div>
      </el-drawer>
      <el-drawer :title="`Add samples to file: ${uploadedFiles[drawerIndex].name}`" :visible.sync="samplePanel"
        :with-header="true" ref="samplesPanel" destroy-on-close>
        <span>
          <id-sampler id="samples-form" :collection="uploadedFiles[drawerIndex].sample_ids"
            @new-samples-input="setSampleIds(drawerIndex, $event)" />
        </span>
        <div class="panel panel-success panel-drawer-tips">
          <div class="panel-heading">
            <h4 class="panel-title">Tips</h4>
          </div>
          <div class="panel-body">
            <ul>
              <li>Keep clicking "New Sample" to to keep adding new file samples then press return or click "Save" when
                you're done typing the name of a sample. When you're done adding sample, you must click "Save" again to
                validate your entries</li>

              <li>The sample name must be already existing in GigaDB.
                If there is a typo or they don't exist, they will be ignored upon finalising
                the upload process
              </li>

              <li>You can alternate adding and removing any number of file samples in this panel with editing the metadata
                in the table on the main form. Your selection won't be lost.
              </li>
              <li>If you leave/reload the web page, the entries made in the panel will be lost.</li>

            </ul>
          </div>
        </div>
      </el-drawer>
    </div>
    <input v-for="(uploadId, index) in filesToDelete" :key="uploadId" type="hidden" :name="`DeleteList[${index}]`"
      :value="uploadId" />

    <div v-for="(attributes, uid) in fileAttributes" :key="uid">
      <div v-for="(attr, idx) in attributes" :key="idx">
        <input type="hidden" :name="`Attributes[${uid}][Attributes][${idx}][name]`" :value="attr['name']" />
        <input type="hidden" :name="`Attributes[${uid}][Attributes][${idx}][value]`" :value="attr['value']" />
        <input type="hidden" :name="`Attributes[${uid}][Attributes][${idx}][unit]`" :value="attr['unit']" />
      </div>
    </div>

  </div>
</template>

<style scoped>
.form-bulk-upload {
  padding: 5em;
}

.panel-tips {
  margin: 1em;
  width: 100%
}

.panel-drawer-tips {
  margin: 1em;
  width: 90%
}

.form-group.required .control-label:after {
  content: "*";
  color: red;
}
</style>

<script>
import { eventBus } from '../index.js'
import AttributeSpecifier from './AttributeSpecifier.vue'
import IdSampler from './IdSampler.vue'

export default {
  components: {
    "attribute-specifier": AttributeSpecifier,
    "id-sampler": IdSampler,
  },
  props: {
    identifier: { type: String }, // Unused?
    token: { type: String }, // Unused?
    uploads: {
      type: Array,
      default: () => []
    },
    attributes: {
      type: Object
    },
    filetypes: {
      type: Object
    }
  },
  data: function () {
    return {
      uploadedFiles: this.uploads || [],
      fileAttributes: this.attributes || [],
      filesToDelete: [],
      metaComplete: [],
      dataTypes: Object.keys(this.filetypes),
      attrPanel: false,
      samplePanel: false,
      drawerIndex: 0,
      selectedUpload: -1,
    }
  },
  methods: {
    // WARN: cannot turn into computed prop because Vue is not reactive to changes in array elements (metaComplete)
    isMetadataComplete() {
      return this.metaComplete.length === this.uploadedFiles.length
    },
    fieldHasChanged(uploadIndex) {
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
    checkFieldsState() {
      for (var uploadIndex = 0; uploadIndex < this.uploadedFiles.length; uploadIndex++) {
        if (this.uploadedFiles[uploadIndex].datatype != undefined && this.uploadedFiles[uploadIndex].datatype.length > 0 && this.uploadedFiles[uploadIndex].description != undefined && this.uploadedFiles[uploadIndex].description.length > 0) {
          this.metaComplete[uploadIndex] = true
          // console.log(`all fields complete for upload ${uploadIndex}`)
        }
      }

      if (this.isMetadataComplete()) {
        // console.log(`Emitting metadata-ready-status`)
        eventBus.$emit('metadata-ready-status', true)
      } else {
        eventBus.$emit('metadata-ready-status', false)
      }
    },
    toggleAttrDrawer(uploadIndex, uploadId) {
      // console.log(`Attr, uploadIndex: ${uploadIndex}, selectedUpload: ${uploadId}`)
      // console.log("filesAttributes:"+JSON.stringify(this.fileAttributes[uploadId]))
      this.drawerIndex = uploadIndex
      this.selectedUpload = uploadId
      this.attrPanel = !this.attrPanel
    },
    toggleSampleDrawer(uploadIndex, uploadId) {
      this.drawerIndex = uploadIndex
      this.selectedUpload = uploadId
      this.samplePanel = !this.samplePanel
      // console.log(`Toogling sample drawer: ${this.samplePanel}`)
    },
    deleteUpload(uploadIndex, uploadId) {
      this.uploadedFiles.splice(uploadIndex, 1)
      this.filesToDelete.push(uploadId)
    },
    setSampleIds(uploadIndex, samples) {
      if (samples) {
        this.uploadedFiles[uploadIndex].sample_ids = samples.join(',')
        // console.log(`Assigned sample_ids ${this.uploadedFiles[uploadIndex].sample_ids}`)
      }
      this.toggleSampleDrawer(uploadIndex, this.selectedUpload)
    },
    handleAttrClose(done) {
      console.log("Closing Attributes panel")
      console.log(JSON.stringify(this.fileAttributes))
      done()
    }
  },
  mounted: function () {
    this.$nextTick(function () {
      eventBus.$emit("stage-changed", "annotating")
      this.checkFieldsState()
    })
  },
  beforeDestroy: function () {
    console.log("before destroy")
    delete this.uploadedfiles
  },
  destroyed: function () {
    console.log("after destroy")
  }
}
</script>