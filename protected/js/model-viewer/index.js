import { createUi } from "./ui/index.js";
import { createModelViewer } from "./viewer/index.js";
import { logger } from "./helpers/logger.js";
import { invariant } from "./helpers/invariant.js";
/**
 * @param {Array} files - Array of file instances expected to follow protected/models/File.php, location is expected to point to a 3D model file, extension is expected to be one of stl, obj, ply, las
 */
export function modelViewer(files) {
  const root = $("#modelViewerRoot");
  const container = root.find(".js-canvas-container");

  invariant(container.length !== 0, "Expected element not found");

  function getFileByProperty(property, value) {
    return files.find((file) => file[property] === value);
  }

  /**
   * @param {{ searchBy: string, value: string, key: string }} args
   * @returns {string | null}
   *
   * example: getFileProperty({ searchBy: "id", value: "123", key: "description" })
   * returns the description of the file with id 123 or null if no such file exists
   */
  function getFileProperty({ searchBy, value, key }) {
    const file = getFileByProperty(searchBy, value);
    if (!file) {
      return null;
    }
    return file[key];
  }

  const { loadModel } = createModelViewer(container);

  const uiState = createUi({
    root,
    onSelect: handleLoadModel,
    onPlay: handleLoadModel,
    getDataProperty: getFileProperty,
  });

  async function handleLoadModel(fileId) {
    if (!fileId) {
      uiState.status = "idle";
      return;
    }

    try {
      uiState.error = null;
      uiState.status = "pending";
      const file = getFileByProperty("id", fileId);
      await loadModel(file);
      uiState.status = "success";
    } catch (err) {
      logger("error", "Error loading model", err);
      uiState.error = err;
      uiState.status = "error";
    }
  }

  // load the currently selected model for development purposes
  handleLoadModel(uiState.selected);

  logger("info", "Model viewer initialized");
}
