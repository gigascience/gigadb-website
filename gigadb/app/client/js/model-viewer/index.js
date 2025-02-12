import { createUi } from "./ui/index.js";
import { createModelViewer } from "./viewer/index.js";
import { logger } from "./helpers/logger.js";
import { invariant } from "./helpers/invariant.js";
import { selector } from "./ui/selectors.js";

const defaultOptions = {
  loadModelOnInit: true,
};

/**
 * @param {Array<Object>} files - Array of file objects representing 3D model files
 * @param {number} files[].id - Unique identifier for the file
 * @param {string} files[].location - URL pointing to the 3D model file
 * @param {string} files[].name - Display name of the file
 * @param {string} files[].extension - File extension, must be one of: 'stl', 'obj', 'ply', 'las'
 * @param {Object} [options] - Configuration options
 * @param {boolean} [options.loadModelOnInit=true] - Whether to load the first model immediately on initialization
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

  if (mergedOptions.loadModelOnInit) {
    handleLoadModel(uiState.selected);
  }
}
