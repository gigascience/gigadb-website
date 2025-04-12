import { OrbitControls } from "three/addons/controls/OrbitControls.js";

export function createControls(camera, canvas, renderer = null) {
  const controls = new OrbitControls(camera, canvas);

  controls.enableDamping = true;
  controls.dampingFactor = 0.1;
  controls.screenSpacePanning = true;

  controls.zoomSpeed = 1.2;

  controls.target.set(0, 0, 0);
  controls.update();

  const updateControlsState = () => {
    controls.enabled = !renderer.xr.isPresenting;
  };

  // toggle controls based on xr non-xr use
  if (renderer?.xr) {
    renderer.xr.addEventListener('sessionstart', updateControlsState);
    renderer.xr.addEventListener('sessionend', updateControlsState);

    updateControlsState();
  }

  controls.tick = () => {
    if (controls.enabled !== false) {
      controls.update();
    }
  };

  controls.destroy = () => {
    controls.dispose();

    if (renderer?.xr) {
      renderer.xr.removeEventListener('sessionstart', updateControlsState);
      renderer.xr.removeEventListener('sessionend', updateControlsState);
    }
  };

  return controls;
}
