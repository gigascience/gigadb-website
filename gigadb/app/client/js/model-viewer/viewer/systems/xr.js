import { invariant } from "../../helpers/invariant.js";
import { createGridHelper } from "../helpers.js";

export function setupXR({ renderer, scene, controls, getModels }) {
  [renderer, scene, controls, getModels].forEach(arg => invariant(arg, `Expected ${arg} to be defined`));

  if (!renderer.xr) return () => null;

  const grid = createGridHelper();
  const originalTransforms = new Map();

  renderer.xr.addEventListener('sessionstart', onSessionStart);
  renderer.xr.addEventListener('sessionend', onSessionEnd);

  return () => {
    renderer.xr.removeEventListener('sessionstart', onSessionStart);
    renderer.xr.removeEventListener('sessionend', onSessionEnd);
  }

  function onSessionStart() {
    controls.saveState();
    controls.enabled = false;
    scene.add(grid);

    getModels().forEach(model => {
      originalTransforms.set(model.uuid, {
        position: model.position.clone(),
        rotation: model.rotation.clone(),
      });

      // ad hoc positon model for XR
      model.position.set(0, 1.6, -4);
      model.rotation.set(0, Math.PI / 2, 0);
    });
  }

  function onSessionEnd() {
    controls.enabled = true;
    scene.remove(grid);

    getModels().forEach(model => {
      const t = originalTransforms.get(model.uuid);
      if (t) {
        model.position.copy(t.position);
        model.rotation.copy(t.rotation);
      }
    });
    originalTransforms.clear();

    controls.reset();
    controls.update();
  }
}