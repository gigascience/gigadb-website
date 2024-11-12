import { DirectionalLight, HemisphereLight } from "three";

const intensityFactor = 2;

function createLights() {
  // NOTE in case of low performance, remove one directional light
  const hemiLight = new HemisphereLight(0xcce0ff, 0x777777, 2 * intensityFactor);
  const sunlight = new DirectionalLight(0xffd7b3, 2.5 * intensityFactor);
  const backLight = new DirectionalLight(0xb3d7ff, 0.2 * intensityFactor);

  sunlight.position.set(-10, 10, 10);
  backLight.position.set(10, -10, -10);

  return [
    sunlight,
    backLight,
    hemiLight,
  ];
}

export { createLights };
