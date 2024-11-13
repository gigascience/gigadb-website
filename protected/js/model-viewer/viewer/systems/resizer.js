import { debounce } from "../../helpers/debounce.js";

export const setSize = ([width, height], camera, renderer) => {
  camera.aspect = width / height;
  camera.updateProjectionMatrix();
  renderer.setSize(width, height);
  renderer.setPixelRatio(window.devicePixelRatio);
};

function createResizer({ getContainerDimensions, camera, renderer, onResize }) {
  setSize(getContainerDimensions(), camera, renderer);

  const debouncedResize = debounce(() => {
    setSize(getContainerDimensions(), camera, renderer);
    onResize();
  }, 100);

  $(window).on("resize", debouncedResize);
  $(window).on("fullscreenchange", debouncedResize);

  function destroy() {
    $(window).off("resize", debouncedResize);
    $(window).off("fullscreenchange", debouncedResize);
    debouncedResize.cancel();
  }

  return {
    destroy,
  };
}

export { createResizer };
