<?php
/**
 * @param array $data An array of external links to 3D models, where each item has:
 *   - id: number (the external link ID)
 *   - dataset_id: number (the dataset this model belongs to)
 *   - url: string (URL to the 3D model file)
 *   - external_link_type_id: number (should be 5 for 3D Models)
 *   - external_link_type_name: string (should be "3D Models")
 */

$files = array_map(function ($item) {
  return [
    'id' => $item['id'],
    'location' => $item['url'],
    'name' => pathinfo($item['url'], PATHINFO_BASENAME),
    'extension' => pathinfo($item['url'], PATHINFO_EXTENSION),
  ];
}, $data);


?>

<div id="hicViewerRoot">
  <form>
    <div class="form-group">
      <label class="control-label" for="hic-selector">Select a HiC file to view:</label>
      <select id="hic-selector" class="form-control js-hic-selector hic-selector test-hic-selector">
        <?php foreach ($files as $index => $file): ?>
          <!-- id is numeric -->
          <option value="<?php echo $file['id']; ?>" <?php echo $index === 0 ? 'selected' : ''; ?>><?php echo $file['name']; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>
  <div class="js-model-description model-description">
    <p class="js-description-content"></p>
  </div>
  <div class="js-hic-viewer hic-viewer"></div>
</div>

<script>
  $(document).ready(function () {
    const $selector = $('.js-hic-selector');
    const $viewer = $('.js-hic-viewer');

    function updateViewer() {
      const selectedOption = $selector.find('option:selected');
      const fileName = selectedOption.text();
      $viewer.text(fileName);
    }

    $selector.on('change', updateViewer);
    updateViewer(); // Show initial selection
  })
</script>
