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

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/juicebox.js@2.4.8/dist/css/juicebox.css">

<div id="hicViewerRoot" class="hic-viewer-root">
  <div class="row">
    <div class="col-md-4">
      <form>
        <div class="form-group">
          <label class="control-label" for="hic-selector">Select a HiC file to view:</label>
          <select id="hic-selector" class="form-control js-hic-selector hic-selector test-hic-selector">
            <option selected disabled>Select a HiC file to view</option>
            <?php foreach ($files as $index => $file): ?>
              <!-- id is numeric -->
              <option value="<?php echo $file['id']; ?>">
                <?php echo $file['name']; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>
    <div class="col-md-12">
      <div class="js-hic-viewer hic-viewer juicebox-app-clone-container"></div>
      <div class="js-hic-error hic-error alert alert-danger mt-10" style="display: none;"></div>
    </div>
  </div>
</div>

<script type="module">
  import juicebox from "https://cdn.jsdelivr.net/npm/juicebox.js@2.4.8/dist/juicebox.esm.js";

  const files = <?php echo json_encode($files); ?>;

  const defaultConfig = {}

  $(document).ready(async function () {
    const $selector = $('.js-hic-selector');
    const $viewer = $('.js-hic-viewer');
    const $error = $('.js-hic-error');

    function getFileConfig(file) {
      return {
        ...defaultConfig,
        url: file.location,
        name: file.name
      }
    }

    async function updateViewer() {
      const selectedOption = $selector.find('option:selected');
      const selectedFile = files.find(f => f.id === parseInt(selectedOption.val()));

      $viewer.empty();

      const config = getFileConfig(selectedFile);

      $error.hide();
      $error.text('');
      try {
        const browser = await juicebox.init($viewer[0], config);
        console.log(`${browser.id} initialized successfully`);
      } catch (error) {
        console.error('Error initializing juicebox:', error);
        $error.text('Error loading HiC viewer: ' + error.message);
        $error.show();
      }
    }

    $selector.on('change', updateViewer);
  })
</script>