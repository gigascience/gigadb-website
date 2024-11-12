import { Scene, Color } from "three";

export function createScene() {
  const scene = new Scene();
  scene.background = new Color(0xf8f8f8);

  return scene;
}
