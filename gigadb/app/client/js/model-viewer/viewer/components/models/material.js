import { MeshPhysicalMaterial, PointsMaterial } from "three";

export function createPointsMaterial(options) {
  return new PointsMaterial({
    size: 0.02,
    sizeAttenuation: true,
    color: 0x888888,
    alphaTest: 0.5,
    transparent: true,
    opacity: 1,
    map: null,
    alphaMap: null,
    vertexColors: false,
    ...options
  });
}

export function createMaterial() {
  const material = new MeshPhysicalMaterial({
    color: 0x9ba3b0,
    metalness: 0.7,
    roughness: 0.3,
    clearcoat: 0.5,
    clearcoatRoughness: 0.2,
    reflectivity: 1,
    envMapIntensity: 1,
  });

  return material;
}