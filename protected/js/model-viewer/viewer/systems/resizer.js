const setSize = ({ width, height }, camera, renderer) => {
  camera.aspect = width / height;
  camera.updateProjectionMatrix();
  renderer.setSize(width, height);
  renderer.setPixelRatio(window.devicePixelRatio);
};

function createResizer(containerDimensions, camera, renderer) {
  let onResize = () => {};

  setSize(containerDimensions, camera, renderer);

  window.addEventListener("resize", handleResize);

  function handleResize() {
    setSize(containerDimensions, camera, renderer);
    onResize();
  }

  function destroy() {
    window.removeEventListener("resize", handleResize);
  }

  return {
    onResize: (callback) => {
      onResize = callback;
    },
    destroy,
  };
}

export { createResizer };
