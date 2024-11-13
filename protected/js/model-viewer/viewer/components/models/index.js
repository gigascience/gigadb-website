import { Mesh } from "three";
import { logger } from "../../../helpers/logger.js";
import { createLoader } from "./loader.js";
import { setupModel } from "./setupModel.js";

/**
 * Loads a 3D model from a given URL and extension
 * @param {Object} params - Parameters object
 * @param {string} params.extension - The extension of the model file, expected to be one of stl, obj, ply, las
 * @param {string} params.url - The URL of the model file
 * @returns {Promise<Mesh>} A promise that resolves to a Three.js Mesh object
 */
export async function load({ extension, url }) {
  const lcExt = extension.toLowerCase();
  logger("info", "Loading model", { extension, url });

  const loader = createLoader(lcExt);
  logger("info", "Created loader", loader);

  let loadedObject = await loader.loadAsync(url);
  logger("info", "Loaded object", loadedObject);

  return setupModel(loadedObject, lcExt);
}
