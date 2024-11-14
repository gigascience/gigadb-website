import { createScene } from "./components/scene.js";
import { createCamera } from "./components/camera.js";
import { createRenderer } from "./systems/renderer.js";
import { createControls } from "./systems/controls.js";
import { createLights } from "./components/lights.js";
import { createResizer } from "./systems/resizer.js";
import { load } from "./components/models/index.js";
import { getContainerDimensions } from "../helpers/getContainerDimensions.js";

/**
 * Creates and manages a 3D model viewer with scene, camera, renderer, and controls
 * @param {HTMLElement} container - DOM element to contain the 3D viewer
 * @returns {Object} Object containing viewer control methods
 * @returns {Function} returns.loadModel - Async function to load and display a 3D model
 * @returns {Function} returns.render - Function to render the current scene
 */
export function createModelViewer(container) {
  let scene;
  let camera;
  let renderer;
  let controls;
  let models = [];
  let onDestroyCallbacks = [];

  function create() {
    scene = createScene();
    camera = createCamera({
      aspectRatio:
        getContainerDimensions(container)[0] /
        getContainerDimensions(container)[1],
    });
    renderer = createRenderer();
    container.append(renderer.domElement);
    controls = createControls(camera, renderer.domElement);

    const lights = createLights();

    scene.add(...lights);

    const { destroy: destroyResizer } = createResizer({
      camera,
      renderer,
      onResize: render,
      container,
    });

    onDestroyCallbacks.push(destroyResizer);

    // re-render when user interacts with the controls
    controls.addEventListener("change", render);
  }

  function render() {
    renderer.render(scene, camera);
  }

  async function loadModel(data) {
    const { location, extension } = data;
    // unload previously loaded model
    if (models.length > 0) {
      scene.remove(...models);
    }
    // reset controls to undo any orbiting done in previous model
    controls.reset();
    models = await load({ location, extension });
    // set orbiting center around model center position
    controls.target.copy(models[0].position);
    scene.add(...models);
    render();
  }

  function unmount() {
    controls.removeEventListener("change", render);
    onDestroyCallbacks.forEach((callback) => callback());
  }

  create();

  $(window).on("beforeunload", () => {
    unmount();
  });

  return {
    render,
    loadModel,
  };
}
