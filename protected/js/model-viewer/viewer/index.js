import { createScene } from "./components/scene.js";
import { createCamera } from "./components/camera.js";
import { createRenderer } from "./systems/renderer.js";
import { createControls } from "./systems/controls.js";
import { createLights } from "./components/lights.js";
import { createResizer } from "./systems/resizer.js";
import { load } from "./components/models/index.js";
import { createCube } from "./components/cube.js";
import { logger } from "../helpers/logger.js";

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

  function create() {
    scene = createScene();
    camera = createCamera({
      aspectRatio: containerDimensions.width / containerDimensions.height,
    });
    renderer = createRenderer();
    container.append(renderer.domElement);
    controls = createControls(camera, renderer.domElement);

    if (containerDimensions.width === 0 || containerDimensions.height === 0) {
      const msg =
        "Container size is zero. Please ensure the container has a defined width and height.";
      logger("error", msg);
      throw new Error(msg);
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

    onDestroyCallbacks.push(destroyResizer);

    // re-render when user interacts with the controls
    controls.addEventListener("change", render);
  }

  function render() {
    renderer.render(scene, camera);
  }

  async function loadModel({ location, extension }) {
    // unload previously loaded model
    if (model) {
      scene.remove(model);
    }
    // reset controls to undo any orbiting done in previous model
    controls.reset();
    model = await load({ url: location, extension });
    // set orbiting center around model center position
    controls.target.copy(model.position);
    scene.add(model);
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
