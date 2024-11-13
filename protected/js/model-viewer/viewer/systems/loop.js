import { Clock } from "three";

const clock = new Clock();

export function createLoop(camera, scene, renderer) {
  const updatables = []

  function start() {
    renderer.setAnimationLoop(animate);
  }

  function animate() {
    tick();
    renderer.render(scene, camera);
  }

  function stop() {
    renderer.setAnimationLoop(null);
  }

  function tick() {
    const delta = clock.getDelta();
    for (const object of updatables) {
      object.tick(delta);
    }
  }

  return {
    start,
    tick,
    stop,
    updatables,
  };
}
