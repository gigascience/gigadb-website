import { PerspectiveCamera } from "three";

export function createCamera({
  aspectRatio = 1
}) {
  const camera = new PerspectiveCamera(45, aspectRatio, 0.1, 100);
  return camera;
}
