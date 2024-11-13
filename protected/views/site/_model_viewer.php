<?php
// sample files
$filenames = [
  'Cube_3d_printing_sample.stl',
  'Menger_sponge_sample.stl',
  'Eiffel_tower_sample.STL',
  'Stanford_Bunny_sample.stl',
  'GeoB8502_825cm_Shell-6.obj',
  'GeoB8502_865cm_Shell-1.obj',
  'GeoB8502_865cm_Shell-2.obj',
  'GeoB8502_865cm_Shell-3.obj',
  'GeoB8502_825cm_Shell-8.obj',
  'leaf_05.las',
  'leaf_06.las',
  'leaf_07.las',
  'leaf_08.las',
  'leaf_09.las',
  'NF66_body_resize_v2.ply',
  '12_K039105_04.ply',
  '22_K039117_03.ply',
  '55_HC5504-3_03.ply',
  '63_K039178_02.ply',
  'scene.gltf',
  '3D_surface_reconstruction_bitis_dentition.stl',
];

$assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.3d-models'));

// mock files for testing, this emulates the Files model
// these files should be provided by the view that rnders this partial
$files = array_map(function ($filename) use ($assetsUrl) {
  $location = $assetsUrl . '/' . $filename;
  return [
    'id' => $location, // use location as id for now
    'dataset_id' => 123,
    'location' => $location,
    'name' => $filename,
    'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    'extension' => pathinfo($filename, PATHINFO_EXTENSION),
    'size' => 1000,
    'date_stamp' => '2024-01-01',
    'format_id' => 1,
    'type_id' => 1,
    'code' => '1234567890',
    'index4blast' => '1234567890',
  ];
}, $filenames);
?>

<div id="modelViewerRoot">
  <div class="form-group">
    <label class="control-label" for="model-selector">Select a model:</label>
    <select id="model-selector" class="form-control js-model-selector model-selector">
      <?php foreach ($files as $index => $file): ?>
        <!-- consider id as value, however location is likely unique too -->
        <option value="<?php echo $file['id']; ?>" <?php echo $index === 0 ? 'selected' : ''; ?>>
          <?php echo $file['name']; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="js-model-description model-description">
    <p class="js-description-content"></p>
  </div>

  <div class="model-viewer-container">
    <div class="canvas-container js-canvas-container">
    </div>
    <div class="js-controls controls" style="display: none;">
      <!-- note: these are links rather than buttons because of bootstrap tooltip -->
      <a href="#" class="js-controls-info-btn controls-btn" data-toggle="tooltip" title="Help">
        <i class="fa fa-question-circle"></i>
        <span class="sr-only">Help</span>
      </a>
      <a href="#" class="js-fullscreen-btn controls-btn" data-toggle="tooltip" title="Toggle fullscreen">
        <i class="fa fa-expand"></i>
        <span class="sr-only">Toggle fullscreen</span>
      </a>
    </div>
    <div class="play-button-overlay js-play-button-overlay">
      <button class="play-button js-play-button">
        <i class="fa fa-play play-button-icon"></i>
        <span class="sr-only">Load model</span>
      </button>
    </div>
    <div class="loading-overlay js-loading-overlay" style="display: none;">
      <div class="loading-spinner"></div>
      <div class="loading-text">Loading model<span aria-hidden="true">...</span></div>
    </div>
    <div class="error-display js-error-display" role="alert">
      <p class="error-content js-error-content" style="display: none;"></p>
    </div>
    <div class="js-help-modal help-modal" style="display: none;">
      <div class="help-modal-content">
        <div class="help-modal-header">
          <h2 class="help-modal-title">NAVIGATION BASICS</h2>
          <button class="js-help-modal-close help-modal-close">
            <i class="fa fa-times"></i>
            <span class="sr-only">Close</span>
          </button>
        </div>
        <div class="help-modal-body">
          <div class="help-control">
            <i class="fa fa-refresh"></i>
            <div class="help-control-text">
              <div>Orbit around</div>
              <div class="help-control-detail">Left click + drag</div>
            </div>
          </div>
          <div class="help-control">
            <i class="fa fa-search"></i>
            <div class="help-control-text">
              <div>Zoom</div>
              <div class="help-control-detail">Scroll anywhere</div>
            </div>
          </div>
          <div class="help-control">
            <i class="fa fa-arrows"></i>
            <div class="help-control-text">
              <div>Pan</div>
              <div class="help-control-detail">Right click + drag</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
// register model-viewer scripts
Yii::app()->assetManager->forceCopy = YII_DEBUG;
$jsDir = Yii::getPathOfAlias('application.js.model-viewer');
$jsUrl = Yii::app()->assetManager->publish($jsDir);
Yii::app()->clientScript->registerScriptFile($jsUrl . '/index.js', CClientScript::POS_END, ['type' => 'module']);
?>

<script type="module">
  import { modelViewer } from "<?php echo $jsUrl; ?>/index.js";

  $(document).ready(function () {
    modelViewer(<?php echo json_encode($files); ?>);
    $('[data-toggle="tooltip"]').tooltip();
  })
</script>