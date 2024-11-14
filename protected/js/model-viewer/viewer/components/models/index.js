import { Mesh } from "three";
import { logger } from "../../../helpers/logger.js";
import { createLoader } from "./loader.js";
import { setupModel } from "./setupModel.js";

/**
 * Loads a 3D model from a given location (URL) and extension
 * @param {Object} params - Parameters object
 * @param {string} params.extension - The extension of the model file, expected to be one of stl, obj, ply, las
 * @param {string} params.location - The location of the model file
 * @returns {Promise<Mesh>} A promise that resolves to a Three.js Mesh object
 */
export async function load({ extension, location }) {
  const lcExt = extension.toLowerCase();
  logger("debug", "Loading model", { extension, location });

  const loader = createLoader(lcExt);
  logger("debug", "Created loader", loader);

  logger("debug", `Loading model from location: ${location}`);
  let loadedObject = await loader.loadAsync(location);
  logger("debug", "Loaded object", loadedObject);

  return setupModel(loadedObject, lcExt);
}
