import { Scene, Color } from "three";

export function createScene() {
  const scene = new Scene();
  scene.background = new Color(0xe5e5e5);

  return scene;
}
