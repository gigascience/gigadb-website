<?php

// sample files
$filenames = [
  'Cube_3d_printing_sample.stl',
  'Menger_sponge_sample.stl',
  'Eiffel_tower_sample.STL',
  'Stanford_Bunny_sample.stl'
];

?>

<div class="form-group">
  <label class="control-label" for="model-selector">Select a model:</label>
  <select id="model-selector" class="form-control js-model-selector" style="width: 300px; margin-bottom: 20px;">
    <?php foreach ($filenames as $filename): ?>
      <option value="<?php echo $filename; ?>"><?php echo $filename; ?></option>
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
  <div id="loading-overlay" class="loading-overlay js-loading-overlay hidden">
    <div class="loading-spinner"></div>
    <div class="loading-text">Loading model...</div>
  </div>
  <div class="error-display js-error-display" role="alert">
    <p class="error-text"></p>
  </div>
</div>


<style>
  .controls-info {
    position: absolute;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    top: 0;
    left: 0;
    padding: 6px 12px;
  }

  .controls-info p {
    margin: 0;
    color: white;
  }

  .model-viewer-container {
    position: relative;
    isolation: isolate;
    width: 992px;
    height: 512px;
  }

  .canvas {
    width: 100%;
    height: 100%;
    background: #F0F0F0;
    z-index: 0;
  }

  .play-button-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1;
  }

  .play-button {
    width: 150px;
    height: 150px;
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
    border: none;
    border-radius: 50%;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .play-button-icon {
    font-size: 70px;
    position: relative;
    /* make button look visually centered */
    left: 6px;
  }

  .loading-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    z-index: 1;
  }

  .loading-overlay.active {
    display: flex;
  }

  .loading-spinner {
    width: 50px;
    height: 50px;
    border: 8px solid #3498db;
    border-top: 5px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 10px;
  }

  .loading-text {
    font-size: 16px;
    color: #333;
  }

  .error-display {
    max-width: 100%;
    position: absolute;
    inset-inline: 12px;
    bottom: 12px;
    margin: 0;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
</style>

<script type="module">
  import * as THREE from 'three';
  import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
  import { STLLoader } from 'three/addons/loaders/STLLoader.js';

  let camera, scene, renderer, controls;

  const height = 512;
  const width = 992;
  const aspectRatio = width / height;

  function showLoadingOverlay() {
    document.querySelector('.js-loading-overlay').classList.remove('hidden');
  }

  function hideLoadingOverlay() {
    document.querySelector('.js-loading-overlay').classList.add('hidden');
  }

  function showPlayButtonOverlay() {
    document.querySelector('.js-play-button-overlay').classList.remove('hidden');
  }

  function hidePlayButtonOverlay() {
    document.querySelector('.js-play-button-overlay').classList.add('hidden');
  }

  function setError(message) {
    if (message) {
      document.querySelector('.js-error-display').classList.add('alert', 'alert-danger');
      document.querySelector('.js-error-display .error-text').textContent = message;
    } else {
      document.querySelector('.js-error-display').classList.remove('alert', 'alert-danger');
    }
  }

  const createModelStateProxy = () => {
    const state = {
      loading: false,
      loaded: false,
      error: null,
    };

    return new Proxy(state, {
      set: function (target, property, value) {
        console.log('set', property, value);

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

    // window.addEventListener('resize', onWindowResize, false);
  }

  function onWindowResize(event) {
    // handle viewport resize
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
      const filename = document.getElementById('model-selector').value;
      const extension = filename.split('.').pop();
      const modelUrl = 'files/3d-models/' + filename;
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

  document.querySelector('.js-model-selector').addEventListener('change', loadModel);
  document.querySelector('.js-play-button').addEventListener('click', loadModel);

  // remove event listeners on page unload
  window.addEventListener('beforeunload', () => {
    document.querySelector('.js-model-selector').removeEventListener('change', loadModel);
    document.querySelector('.js-play-button').removeEventListener('click', loadModel);
  });
</script>