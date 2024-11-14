import { createUi } from "./ui/index.js";
import { createModelViewer } from "./viewer/index.js";
import { logger } from "./helpers/logger.js";
import { invariant } from "./helpers/invariant.js";
import { selector } from "./ui/selectors.js";

const defaultOptions = {
  loadModelOnInit: true,
};

/**
 * @param {Array} files - Array of file instances expected to follow protected/models/File.php, location is expected to point to a 3D model file, extension is expected to be one of stl, obj, ply, las
 */
export function modelViewer(files, options = {}) {
  const mergedOptions = { ...defaultOptions, ...options };

  const root = $(selector.root);
  const container = root.find(selector.canvasContainer);

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
    logger("debug", "handleLoadModel with fileId", fileId);
    if (!fileId) {
      uiState.status = "idle";
      return;
    }

    try {
      uiState.error = null;
      uiState.status = "pending";
      const file = getFileByProperty("id", fileId);
      logger("debug", "Loading model", file);
      await loadModel(file);
      uiState.status = "success";
    } catch (err) {
      logger("error", "Error loading model", err);
      uiState.error = err;
      uiState.status = "error";
    }
  }

  // load the currently selected model for development purposes
  if (mergedOptions.loadModelOnInit) {
    logger("debug", "Loading model on init", uiState.selected);
    handleLoadModel(uiState.selected);
  }

  logger("debug", "Model viewer initialized with files", files);
}
