<template>
  <div>
    <p class="mb-20">Please, use this table to annotate the files you've just uploaded with metadata. Once you're done
      with mandatory
      fields (Data Type and Description) for all files, the "Complete and return to Your Uploaded Datasets page" button
      will
      be enabled at the bottom of the page. You must click it to effect your file submission.</p>
    <div>
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th scope="col">File Name</th>
            <th scope="col" id="dataTypeTh">Data Type</th>
            <th scope="col">Format</th>
            <th scope="col">Size</th>
            <th scope="col" id="descriptionTh">Description</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(upload, index) in uploadedFiles" :key="`${upload.id}-uploadedfiles`">
            <td><span :id="`${upload.id}File`" data-toggle="tooltip" data-placement="bottom"
                :title="`md5:${upload.initial_md5}`">{{
                  upload.name }}</span>
              <input type="hidden" :name="`Upload[${upload.id}][name]`" :value="upload.name">
            </td>
            <td>
              <div class="form-group m-0">
                <select v-model="upload.datatype" :name="`Upload[${upload.id}][datatype]`"
                  :id="`upload-${(index + 1)}-datatype`" @change="fieldHasChanged(index, $event)"
                  :aria-labelledy="`${upload.id}File dataTypeTh`" class="form-control td-content">
                  <option v-for="datatype in dataTypes" :key="`${datatype}-datatype`">{{ datatype }}</option>
                </select>
              </div>
            </td>
            <td>{{ upload.extension }}</td>
            <td>{{ upload.size }}</td>
            <td>
              <div class="form-group m-0">
                <input v-model="upload.description" class="form-control td-content" type="text"
                  :name="`Upload[${upload.id}][description]`" :id="`upload-${(index + 1)}-description`"
                  @input="fieldHasChanged(index, $event)" required aria-required="true"
                  :aria-labelledy="`${upload.id}File descriptionTh`">
              </div>
            </td>
            <td>
              <input type="hidden" :name="`Upload[${upload.id}][sample_ids]`" :id="`upload-${index + 1}-sample_ids`"
                :value="upload.sample_ids">
              <div class="btns-row btns-row-center m-0">
                <el-button :id="`upload-${index + 1}-tag`" @click="toggleAttrDrawer(index, upload.id)" type="info"
                  :class="`attribute-button ${upload.name}`">
                  View attributes
                </el-button>
                <!-- NOTE fuw-sample-ids Sample ID button -->
                <!-- <el-button :id="`upload-${index + 1}-sample`" @click="toggleSampleDrawer(index, upload.id)" type="info"
                  :class="`btn btn-green btn-small sample-button ${upload.name}`">
                  Sample IDs
                </el-button> -->
                <el-button :id="`upload-${index + 1}-delete`" :class="`delete-button-${index}`" type="danger"
                  icon="el-icon-delete" @click="deleteUpload(index, upload.id)" circle
                  :aria-label="`delete file ${upload.name}`"></el-button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>


    <aside>
      <p class="mb-20">If you have many files, you may wish to prepare the information in a spreadsheet and upload that
        using the form
        below. The metadata table above will be overwritten to reflect the content of the spreadsheet.

        The uploader will only parse CSV and TSV files. Do not try to upload in other formats.

        With this method of bulk metadata upload, you can also associate references to existing samples and to up to five
        existing file attributes. (if the sample or the file attribute do not exist in the GigaDB, they are simply
        ignored).</p>
      <div class="row">
        <div class="col-md-8">
          <bulk-metadata-upload />
        </div>
        <div class="col-md-4">
          <div class="panel tips-panel">
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
                    <!-- NOTE fuw-sample-ids uncomment code below -->
                    <li> TSV:
                      <pre>File Name    Data Type   File Format     Description   <!--  Sample IDs --> Attribute 1     Attribute 2     Attribute 3     Attribute 4     Attribute 5</pre>
                    </li>
                    <li> CSV:
                      <pre>File Name, Data Type, File Format, Description,<!-- Sample IDs,--> Attribute 1, Attribute 2, Attribute 3, Attribute 4, Attribute 5</pre>
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
      <accessible-drawer :title="`Add attributes to file: ${uploadedFiles[drawerIndex].name}`" :visible.sync="attrPanel"
        :with-header="true" :before-close="handleAttrClose" destroy-on-close>
        <div class="attributes-drawer-body">
          <span>
            <attribute-specifier id="attributes-form" :fileAttributes="fileAttributes[selectedUpload]" />
          </span>
          <div class="container-fluid">
            <div class="panel tips-panel panel-drawer-tips">
              <div class="panel-heading">
                <h3 class="h4 panel-title">Tips</h3>
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
          </div>
        </div>
      </accessible-drawer>

      <!-- NOTE fuw-sample-ids Sample ID drawer -->
      <!-- <el-drawer :title="`Add samples to file: ${uploadedFiles[drawerIndex].name}`" :visible.sync="samplePanel"
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
      </el-drawer> -->
    </div>
    <input v-for="(uploadId, index) in filesToDelete" :key="`${uploadId}-filesToDelete`" type="hidden"
      :name="`DeleteList[${index}]`" :value="uploadId" />

    <div v-for="(attributes, uid) in fileAttributes" :key="`${uid}-fileAttrs`">
      <div v-for="(attr, idx) in attributes" :key="`${idx}-fileAttr`">
        <input type="hidden" :name="`Attributes[${uid}][Attributes][${idx}][name]`" :value="attr['name']" />
        <input type="hidden" :name="`Attributes[${uid}][Attributes][${idx}][value]`" :value="attr['value']" />
        <input type="hidden" :name="`Attributes[${uid}][Attributes][${idx}][unit]`" :value="attr['unit']" />
      </div>
    </div>
    <file-annotator-submit-button :disabled="!isMetadataComplete" />
  </div>
</template>

<style scoped>
.panel-tips {
  margin: 1em;
  width: 100%
}

.panel-drawer-tips {
  margin-top: 1em;
}

.form-group.required .control-label:after {
  content: "*";
  color: red;
}

.td-content {
  height: 39px;
}

.attributes-drawer-body {
  margin-top: 5px;
  padding-inline: 10px;
}
</style>

<script>
import AttributeSpecifier from './AttributeSpecifier.vue'
import IdSampler from './IdSampler.vue'
import FileAnnotatorSubmitButton from './FileAnnotatorSubmitButton.vue'
import AccessibleDrawer from './AccessibleDrawer.vue'
import BulkMetadataUpload from './BulkMetadataUpload.vue'

export default {
  components: {
    "attribute-specifier": AttributeSpecifier,
    "id-sampler": IdSampler,
    "file-annotator-submit-button": FileAnnotatorSubmitButton,
    "accessible-drawer": AccessibleDrawer,
    "bulk-metadata-upload": BulkMetadataUpload
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
      attrPanel: false,
      samplePanel: false,
      drawerIndex: 0,
      selectedUpload: -1,
      showTrapFocus: false
    }
  },
  computed: {
    isMetadataComplete: function () {
      return this.metaComplete.length === this.uploadedFiles.length
    },
    dataTypes: function () {
      return Object.keys(this.filetypes)
    }
  },
  methods: {
    fieldHasChanged(uploadIndex) {
      if (this.uploadedFiles[uploadIndex].datatype != undefined && this.uploadedFiles[uploadIndex].datatype.length > 0 && this.uploadedFiles[uploadIndex].description != undefined && this.uploadedFiles[uploadIndex].description.length > 0) {
        this.metaComplete.includes(uploadIndex) || this.metaComplete.push(uploadIndex)
      } else {
        this.metaComplete = this.metaComplete.filter(val => val !== uploadIndex)
      }
    },
    checkFieldsState() {
      for (let uploadIndex = 0; uploadIndex < this.uploadedFiles.length; uploadIndex++) {
        if (this.uploadedFiles[uploadIndex].datatype != undefined && this.uploadedFiles[uploadIndex].datatype.length > 0 && this.uploadedFiles[uploadIndex].description != undefined && this.uploadedFiles[uploadIndex].description.length > 0) {
          this.metaComplete.includes(uploadIndex) || this.metaComplete.push(uploadIndex)
          // console.log(`all fields complete for upload ${uploadIndex}`)
        }
      }
    },
    toggleAttrDrawer(uploadIndex, uploadId) {
      // console.log(`Attr, uploadIndex: ${uploadIndex}, selectedUpload: ${uploadId}`)
      // console.log("filesAttributes:"+JSON.stringify(this.fileAttributes[uploadId]))
      this.drawerIndex = uploadIndex
      this.selectedUpload = uploadId
      this.attrPanel = !this.attrPanel
    },
    deleteUpload(uploadIndex, uploadId) {
      this.uploadedFiles.splice(uploadIndex, 1)
      this.filesToDelete.push(uploadId)
    },

    // Sample ID methods:
    // toggleSampleDrawer(uploadIndex, uploadId) {
    //   this.drawerIndex = uploadIndex
    //   this.selectedUpload = uploadId
    //   this.samplePanel = !this.samplePanel
    //   // console.log(`Toogling sample drawer: ${this.samplePanel}`)
    // },
    // setSampleIds(uploadIndex, samples) {
    //   if (samples) {
    //     this.uploadedFiles[uploadIndex].sample_ids = samples.join(',')
    //     // console.log(`Assigned sample_ids ${this.uploadedFiles[uploadIndex].sample_ids}`)
    //   }
    //   this.toggleSampleDrawer(uploadIndex, this.selectedUpload)
    // },

    handleAttrClose(done) {
      console.log("Closing Attributes panel")
      console.log(JSON.stringify(this.fileAttributes))
      done()
    }
  },
  mounted: function () {
    this.$nextTick(function () {
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