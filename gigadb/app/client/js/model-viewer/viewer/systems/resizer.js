import { debounce } from "../../helpers/debounce.js";
import { getContainerDimensions } from "../../helpers/getContainerDimensions.js";

const setSize = ([width, height], camera, renderer) => {
  camera.aspect = width / height;
  camera.updateProjectionMatrix();
  renderer.setSize(width, height);
  renderer.setPixelRatio(window.devicePixelRatio);
};

function createResizeObserver(onResize) {
  const resizeObserver = new ResizeObserver((entries) => {
    for (const entry of entries) {
      if (entry.contentRect.width > 0 && entry.contentRect.height > 0) {
        onResize();
      }
    }
  });

  return resizeObserver;
}

function createResizer({
  camera,
  renderer,
  onResize,
  container,
}) {
  setSize(getContainerDimensions(container), camera, renderer);

  const debouncedResize = debounce(() => {
    setSize(getContainerDimensions(container), camera, renderer);
    onResize();
  }, 100);

  $(window).on("resize", debouncedResize);
  $(window).on("fullscreenchange", debouncedResize);

  const observer = createResizeObserver(debouncedResize);
  observer.observe(container[0]);

  function destroy() {
    $(window).off("resize", debouncedResize);
    $(window).off("fullscreenchange", debouncedResize);
    debouncedResize.cancel();
    observer.unobserve(container[0]);
    observer.disconnect();
  }

  return {
    destroy,
  };
}

export { createResizer };
