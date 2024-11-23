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
          <label class="control-label" for="hic-selector">Select a HiC file to view</label>
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
      <div class="hic-error-display js-hic-error-display" role="alert">
        <p class="hic-error-content js-hic-error-content" style="display: none;"></p>
      </div>
      <div class="js-hic-viewer hic-viewer juicebox-app-clone-container"></div>
    </div>
  </div>
</div>

<script type="module">
  import juicebox from "https://cdn.jsdelivr.net/npm/juicebox.js@2.4.8/dist/juicebox.esm.js";

  const files = <?php echo json_encode($files); ?>;
  const defaultConfig = {}

  function getFileConfig(file) {
    return {
      ...defaultConfig,
      url: file.location,
      name: file.name
    }
  }

  function getFileFromId(id) {
    return files.find(f => f.id === parseInt(id));
  }

  $(document).ready(async function () {
    const $hicViewer = $('.js-hic-viewer');
    const $hicSelect = $('.js-hic-selector');
    const $hicError = $('.js-hic-error-display');
    const $hicErrorContent = $('.js-hic-error-content');

    if (!$hicViewer || !$hicSelect) {
      console.error('Required elements not found');
      return;
    }

    const hicBrowser = await juicebox.init($hicViewer[0], defaultConfig);

    async function handleSelect() {
      setError(null);

      try {
        const selectedFile = getFileFromId($hicSelect.val());
        if (!selectedFile) {
          throw new Error('No file selected');
        }
        await hicBrowser.loadHicFile(getFileConfig(selectedFile));
      } catch (error) {
        setError(error.message);
      }
    }

    function setError(message) {
      if (message) {
        $hicError.addClass(['alert', 'alert-danger']);
        $hicErrorContent.text(message);
        $hicErrorContent.show();
      } else {
        $hicError.removeClass(['alert', 'alert-danger']);
        $hicErrorContent.text('');
        $hicErrorContent.hide();
      }
    }

    $hicSelect.on('change', handleSelect);

    $(window).on('beforeunload', function () {
      $hicSelect.off('change', handleSelect);
    });
  })
</script>