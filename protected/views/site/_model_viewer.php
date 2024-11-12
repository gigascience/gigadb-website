<?php
// sample files
$filenames = [
  'Cube_3d_printing_sample.stl',
  'Menger_sponge_sample.stl',
  'Eiffel_tower_sample.STL',
  'Stanford_Bunny_sample.stl',
  'GeoB8502_825cm_Shell-6.obj',
  'leaf_09.las',
  'NF66_body_resize_v2.ply',
  '3D_surface_reconstruction_bitis_dentition.stl'
];

$assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.3d-models'));

// mock files for testing, this emulates the Files model
$files = array_map(function ($filename) use ($assetsUrl) {
  return [
    'id' => 1,
    'dataset_id' => 123,
    'location' => $assetsUrl . '/' . $filename,
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

<div class="form-group">
  <label class="control-label" for="model-selector">Select a model:</label>
  <select id="model-selector" class="form-control js-model-selector model-selector">
    <?php foreach ($files as $file): ?>
      <option value="<?php echo $file['location']; ?>"><?php echo $file['name']; ?></option>
    <?php endforeach; ?>
  </select>
</div>

<div id="model-viewer-container" class="model-viewer-container">
  <div id="model-view-container" class="model-view-container js-model-view-container">
  </div>
  <div class="controls-info">
    <p>
      Left click + drag: Rotate<br>
      Right click + drag: Pan<br>
      Mouse wheel: Zoom
    </p>
  </div>
  <div id="play-button-overlay" class="play-button-overlay js-play-button-overlay">
    <button id="play-button" class="play-button js-play-button">
      <i class="fa fa-play play-button-icon"></i>
      <span class="sr-only">Play</span>
    </button>
  </div>
  <div id="loading-overlay" class="loading-overlay js-loading-overlay" style="display: none;">
    <div class="loading-spinner"></div>
    <div class="loading-text">Loading model...</div>
  </div>
  <div class="error-display js-error-display" role="alert">
    <p class="error-content" style="display: none;"></p>
  </div>
</div>

<script>
  // print to console the model urls from php
  console.log(<?php echo json_encode($files); ?>);
</script>


<?php
Yii::app()->assetManager->forceCopy = YII_DEBUG;
$jsDir = Yii::getPathOfAlias('application.js.model-viewer');
$jsUrl = Yii::app()->assetManager->publish($jsDir);
Yii::app()->clientScript->registerScriptFile($jsUrl . '/index.js', CClientScript::POS_END, ['type' => 'module']);
