import { OrbitControls } from "three/addons/controls/OrbitControls.js";

export function createControls(camera, canvas) {
  const controls = new OrbitControls(camera, canvas);

  controls.enableDamping = true;
  controls.dampingFactor = 0.05;
  controls.screenSpacePanning = true;

  controls.zoomSpeed = 1.2;

  camera.position.set(5, 5, 5);
  controls.target.set(0, 0, 0);
  controls.update();

  return controls;
}
