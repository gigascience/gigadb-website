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

<div id="modelViewerRoot">
  <form>
    <div class="form-group">
      <label class="control-label" for="model-selector">Select a model:</label>
      <select id="model-selector" class="form-control js-model-selector model-selector test-model-selector">
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

  <div class="model-viewer-container js-model-viewer-container">
    <div class="canvas-container js-canvas-container">
    </div>
    <div class="js-controls controls" style="display: none;">
      <!-- note: these are links rather than buttons because of bootstrap tooltip -->
      <a href="#" class="js-controls-info-btn controls-btn" data-toggle="tooltip" title="Help (h)">
        <i class="fa fa-question-circle"></i>
        <span class="sr-only">Toggle help</span>
      </a>
      <a href="#" class="js-fullscreen-btn controls-btn" data-toggle="tooltip" title="Fullscreen (f)">
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
    <div class="js-loading-overlay">
      <div role="status" aria-live="polite">
        <span class="sr-only js-loading-text"></span>
      </div>
      <div class="loading-display loading-overlay js-loading-display" style="display: none;">
        <div class="loading-spinner"></div>
        <div class="loading-text js-loading-text"></div>
      </div>
    </div>
    <div class="error-display js-error-display" role="alert">
      <p class="error-content js-error-content" style="display: none;"></p>
    </div>
    <div class="js-help-modal help-modal" style="display: none;">
      <div class="help-modal-content">
        <div class="help-modal-header">
          <h2 class="help-modal-title">CONTROLS</h2>
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
// register a script that adds the importmap to the head, so that it is only added once and only to the pages that use this partial
Yii::app()->clientScript->registerScript(
  'import-map', <<<EOD
  (function() {
      const script = document.createElement('script');
      script.type = 'importmap';
      script.textContent = JSON.stringify({
          "imports": {
              "three": "https://cdn.jsdelivr.net/npm/three@0.170.0/build/three.module.js",
              "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.170.0/examples/jsm/"
          }
      });
      document.head.appendChild(script);
  })();
  EOD,
  CClientScript::POS_HEAD
);

Yii::app()->assetManager->forceCopy = YII_DEBUG;
$jsDir = Yii::getAlias('/gigadb/app/client/js/model-viewer');
$jsUrl = Yii::app()->assetManager->publish($jsDir);

Yii::app()->clientScript->registerScriptFile($jsUrl . '/index.js', CClientScript::POS_END, ['type' => 'module']);
?>

<script type="module">
  import { modelViewer } from "<?php echo $jsUrl; ?>/index.js";

  $(document).ready(function () {
    modelViewer(<?php echo json_encode($files); ?>, {
      loadModelOnInit: false
    });
    $('[data-toggle="tooltip"]').tooltip();
  })
</script>