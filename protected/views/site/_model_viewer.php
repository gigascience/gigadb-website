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
  <canvas id="3d-model-canvas" class="canvas js-canvas"></canvas>
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
  <div id="loading-overlay" class="loading-overlay js-loading-overlay">
    <div class="loading-spinner"></div>
    <div class="loading-text">Loading model...</div>
  </div>
  <div class="error-display js-error-display" role="alert">
    <p class="error-text"></p>
  </div>
</div>

<script type="module">
  import * as THREE from 'three';
  import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
  import { STLLoader } from 'three/addons/loaders/STLLoader.js';

  let camera, scene, renderer, controls;

  const height = 512;
  const width = 992;
  const aspectRatio = width / height;


  $(document).ready(function () {
    const loadingOverlay = $('.js-loading-overlay');
    const playButtonOverlay = $('.js-play-button-overlay');
    const playButton = $('.js-play-button');
    const errorDisplay = $('.js-error-display');
    const modelSelector = $('.js-model-selector');

    const createModelStateProxy = () => {
      const state = {
        loading: false,
        loaded: false,
        error: null,
      };

      return new Proxy(state, {
        set: function (target, property, value) {
          console.log('set', target, property, value);
          target[property] = value;

          if (property === 'loading') {
            if (value) {
              showLoadingOverlay();
              hidePlayButtonOverlay();
            } else {
              hideLoadingOverlay();
            }
          }

          if (property === 'loaded') {
            if (value) {
              hideLoadingOverlay();
              hidePlayButtonOverlay();
            } else {
              showPlayButtonOverlay();
            }
          }

          if (property === 'error') {
            if (value) {
              setError(value);
            }
          }

          return true;
        }
      });
    };

    const modelState = createModelStateProxy();

    function showLoadingOverlay() {
      loadingOverlay.show();
    }

    function hideLoadingOverlay() {
      loadingOverlay.hide();
    }

    function showPlayButtonOverlay() {
      playButtonOverlay.show();
    }

    function hidePlayButtonOverlay() {
      playButtonOverlay.hide();
    }

    function setError(message) {
      if (message) {
        errorDisplay.addClass('alert', 'alert-danger');
        errorDisplay.find('.error-text').text(message);
      } else {
        errorDisplay.removeClass('alert', 'alert-danger');
      }
    }

    // handle three.js setup
    function initThree() {
      scene = new THREE.Scene();
      camera = new THREE.PerspectiveCamera(40, aspectRatio, 0.001, 1000);
      renderer = new THREE.WebGLRenderer({
        canvas: document.getElementById('3d-model-canvas'),
        antialias: true
      });
      renderer.setSize(width, height);
      renderer.setPixelRatio(window.devicePixelRatio);
      renderer.setClearColor(0xf0f0f0);

      controls = new OrbitControls(camera, renderer.domElement);
      controls.enableDamping = true;
      controls.dampingFactor = 0.05;
      controls.screenSpacePanning = true;

      controls.zoomSpeed = 1.2;

      camera.position.set(5, 5, 5);
      controls.target.set(0, 0, 0);
      controls.update();

      $(window).on('resize', onWindowResize);
    }

    function onWindowResize(event) {
      renderer.setSize(width, height);
      camera.aspect = width / height;
      camera.updateProjectionMatrix();
    }

    async function loadSTLModel(url) {
      const loader = new STLLoader();
      const geometry = await loader.loadAsync(url);
      geometry.center();
      const material = new THREE.MeshNormalMaterial({
        flatShading: true
      });
      const mesh = new THREE.Mesh(geometry, material);

      // Scale model to fit view
      const box = new THREE.Box3().setFromObject(mesh);
      const size = box.getSize(new THREE.Vector3()).length();
      const scale = 5 / size;
      mesh.scale.set(scale, scale, scale);

      scene.add(mesh);

      function animate() {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
      }
      animate();
    }

    async function loadModel() {
      modelState.loaded = false;
      modelState.loading = true;
      try {
        initThree();
        const modelUrl = modelSelector.val();
        const extension = modelUrl.split('.').pop();
        if (extension.toLowerCase() === 'stl') {
          await loadSTLModel(modelUrl);
        }
      } catch (error) {
        console.error(error);
        modelState.error = error;
      } finally {
        modelState.loading = false;
        modelState.loaded = true;
      }
    }

    function initUi() {
      loadingOverlay.hide();
      playButtonOverlay.show();
      errorDisplay.hide();
      modelSelector.on('change', loadModel);
      playButton.on('click', loadModel);
    }

    initUi();

    $(window).on('beforeunload', () => {
      modelSelector.off('change', loadModel);
      playButton.off('click', loadModel);
    })
  })

</script>