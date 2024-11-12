import { MeshPhysicalMaterial } from "three";

export function createMaterial() {
  const material = new MeshPhysicalMaterial({
    color: 0x9ba3b0, // Lighter gray with slight blue tint
    metalness: 0.7,
    roughness: 0.3,
    clearcoat: 0.5,
    clearcoatRoughness: 0.2,
    reflectivity: 1,
    envMapIntensity: 1,
  });

  return material;
}