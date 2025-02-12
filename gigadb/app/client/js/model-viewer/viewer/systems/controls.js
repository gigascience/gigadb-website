import { OrbitControls } from "three/addons/controls/OrbitControls.js";

export function createControls(camera, canvas) {
  const controls = new OrbitControls(camera, canvas);

  controls.enableDamping = true;
  controls.dampingFactor = 0.1;
  controls.screenSpacePanning = true;

  controls.zoomSpeed = 1.2;

  controls.target.set(0, 0, 0);
  controls.update();

  controls.tick = () => controls.update();

  return controls;
}
