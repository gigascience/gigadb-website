<template>
  <div class="panel tips-panel">
    <div class="panel-heading">
      <h2 class="h4 panel-title">Tips</h2>
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
            <li><abbr title="tab separate values">TSV</abbr>:
              <pre>File Name    Data Type   File Format     Description   <!--  Sample IDs --> Attribute 1     Attribute 2     Attribute 3     Attribute 4     Attribute 5</pre>
            </li>
            <li><abbr title="comma separated values">CSV</abbr>:
              <pre>File Name, Data Type, File Format, Description,<!-- Sample IDs,--> Attribute 1, Attribute 2, Attribute 3, Attribute 4, Attribute 5</pre>
            </li>
          </ul>
        </li>
        <li> When adding attributes, enter each one with the format <code>name::value::unit</code></li>
        <li> If there is no unit, the last <code>::</code> is still needed: <code>name::value::</code></li>
        <li> After uploading the spreadsheet, you can still tweak the metadata in the table above</li>
      </ul>

      <p>Here is an example of a valid spreadsheet to illustrate these requirements:</p>
      <ul>
        <li><a href="/files/examples/bulk-data-upload-example.csv">bulk-data-upload-example.csv</a></li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
.panel-tips {
  margin: 1em;
  width: 100%
}
</style>

<script>
export default {
  name: 'FileAnnotatorTipsPanel'
}
</script>