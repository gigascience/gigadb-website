import {
  Scene,
  PerspectiveCamera,
  WebGLRenderer,
  Mesh,
  MeshNormalMaterial,
  Box3,
  Vector3,
} from "three";
// alternative: import from CDN directly as module: import ... from "https://cdn.skypack.dev/three@0.132.2"
import { OrbitControls } from "three/addons/controls/OrbitControls.js";
import { STLLoader } from "three/addons/loaders/STLLoader.js";
import { createUi } from "./ui/index.js";

let camera, scene, renderer, controls;

const height = 512;
const width = 992;
const aspectRatio = width / height;

$(document).ready(function () {
  const canvas = $(".js-canvas");
  const loadingOverlay = $(".js-loading-overlay");
  const playButtonOverlay = $(".js-play-button-overlay");
  const errorDisplay = $(".js-error-display");
  const modelSelector = $(".js-model-selector");

  const { setup, destroy, modelState } = createUi({
    loadingOverlay,
    playButtonOverlay,
    errorDisplay,
    modelSelector,
  });

  // handle js setup
  function initThree() {
    scene = new Scene();
    camera = new PerspectiveCamera(40, aspectRatio, 0.001, 1000);
    renderer = new WebGLRenderer({
      canvas: canvas.get(0),
      antialias: true,
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

    $(window).on("resize", onWindowResize);
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
    const material = new MeshNormalMaterial({
      flatShading: true,
    });
    const mesh = new Mesh(geometry, material);

    // Scale model to fit view
    const box = new Box3().setFromObject(mesh);
    const size = box.getSize(new Vector3()).length();
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
      const extension = modelUrl.split(".").pop();
      if (extension.toLowerCase() === "stl") {
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

  setup();

  $(window).on("beforeunload", destroy);
});
