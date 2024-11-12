import { MeshNormalMaterial } from "three";

export function createMaterial() {
  const material = new MeshNormalMaterial({
    flatShading: true,
  });

  return material;
}