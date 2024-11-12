import { DirectionalLight, HemisphereLight } from "three";

function createLights() {
  // NOTE in case of low performance, remove one directional light
  const hemiLight = new HemisphereLight(0xcce0ff, 0x555555, 2);
  const sunlight = new DirectionalLight(0xffd7b3, 2.5);
  const backLight = new DirectionalLight(0xb3d7ff, 1.5);

  sunlight.position.set(5, 10, 7);
  backLight.position.set(-5, -10, -7);

  return [sunlight, backLight, hemiLight];
}

export { createLights };
