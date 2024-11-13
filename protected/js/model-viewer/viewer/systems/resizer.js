const setSize = ([width, height], camera, renderer) => {
  camera.aspect = width / height;
  camera.updateProjectionMatrix();
  renderer.setSize(width, height);
  renderer.setPixelRatio(window.devicePixelRatio);
};

function createResizer(getContainerDimensions, camera, renderer) {
  let onResize = () => {};

  setSize(getContainerDimensions(), camera, renderer);

  window.addEventListener("resize", handleResize);

  function handleResize() {
    setSize(getContainerDimensions(), camera, renderer);
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
