import { createScene } from "./components/scene.js";
import { createCamera } from "./components/camera.js";
import { createRenderer } from "./systems/renderer.js";
import { createControls } from "./systems/controls.js";
import { createLights } from "./components/lights.js";
import { createResizer } from "./systems/resizer.js";
import { load } from "./components/models/index.js";
import { createCube } from "./components/cube.js";
import { logger } from "../helpers/logger.js";
import { createAxesHelper, createGridHelper } from "./helpers.js";

export function createModelViewer(container) {
  let scene;
  let camera;
  let renderer;
  let controls;
  let model;
  let onDestroyCallbacks = [];

  const containerDimensions = {
    width: container.innerWidth(),
    height: container.innerHeight(),
  };

  function init() {
    scene = createScene();
    camera = createCamera({
      aspectRatio: containerDimensions.width / containerDimensions.height,
    });
    renderer = createRenderer();
    container.append(renderer.domElement);
    controls = createControls(camera, renderer.domElement);

    if (containerDimensions.width === 0 || containerDimensions.height === 0) {
      logger(
        "error",
        "Container size is zero. Please ensure the container has a defined width and height."
      );
      return;
    }

    logger("info", "Container dimensions:", containerDimensions);

    const lights = createLights();

    // loading a model for testing
    model = createCube();
    // set orbiting center around modle center position
    controls.target.copy(model.position);
    // set camera to look at model center position
    camera.lookAt(model.position);
    scene.add(...lights, model);

    const { destroy: destroyResizer } = createResizer(
      containerDimensions,
      camera,
      renderer
    );

    scene.add(createAxesHelper(), createGridHelper());

    onDestroyCallbacks.push(destroyResizer);

    controls.addEventListener("change", render);
  }

  function render() {
    renderer.render(scene, camera);
  }

  async function loadModel(url) {
    // unload previously loaded model
    if (model) {
      scene.remove(model);
    }

    model = await load({ url, extension: url.split(".").pop() });
    // set orbiting center around modle center position
    controls.target.copy(model.position);
    // set camera to look at model center position
    camera.lookAt(model.position);
    scene.add(model);
    render();
  }

  function destroy() {
    controls.removeEventListener("change", render);
    onDestroyCallbacks.forEach((callback) => callback());
  }

  return {
    init,
    render,
    loadModel,
    destroy,
  };
}
