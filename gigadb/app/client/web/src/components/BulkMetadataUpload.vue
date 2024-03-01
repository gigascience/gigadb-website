<template>
  <form id="bulkUploadForm" method="post" enctype="multipart/form-data"
    class="form-horizontal well form-bulk-upload" aria-labelledby="bulkUploadFormTitle">
    <div>
      <h2 class="h5 form-title" id="bulkUploadFormTitle">Bulk upload metadata</h2>
      <div class="form-group">
        <label for="bulkmetadata" class="col-sm-2 control-label">Select a spreadsheet:</label>
        <div class="col-sm-10">
          <input class="form-control" type="file" id="bulkmetadata" name="bulkmetadata" accept=".csv, .tsv">
        </div>
      </div>
      <div class="btns-row btns-row-end m-0">
        <button class="btn background-btn" type="submit">Upload metadata from spreadsheet</button>
      </div>
    </div>
  </form>
</template>

<style scoped>
.form-bulk-upload {
  padding: 1.5em;
}
.form-title {
  margin-top: 0;
}
.control-label {
  text-align: left;
}
</style>

<script>
export default {
  name: 'BulkMetadataUpload'
}
</script>