import { OrbitControls } from "three/addons/controls/OrbitControls.js";

export function createControls(camera, canvas, renderer = null) {
  const controls = new OrbitControls(camera, canvas);

  controls.enableDamping = true;
  controls.dampingFactor = 0.1;
  controls.screenSpacePanning = true;

  controls.zoomSpeed = 1.2;

  controls.target.set(0, 0, 0);
  controls.update();

  // Only set up VR controls if renderer is provided and has XR support
  if (renderer?.xr) {
    // Function to toggle controls based on VR state
    const updateControlsState = () => {
      controls.enabled = !renderer.xr.isPresenting;
    };

    // Subscribe to VR session changes
    renderer.xr.addEventListener('sessionstart', updateControlsState);
    renderer.xr.addEventListener('sessionend', updateControlsState);

    // Initial state check
    updateControlsState();
  }

  controls.tick = () => {
    if (controls.enabled !== false) {
      controls.update();
    }
  };

  return controls;
}
