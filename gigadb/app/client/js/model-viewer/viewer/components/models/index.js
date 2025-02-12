import { Mesh } from "three";
import { createLoader } from "./loader.js";
import { setupModel } from "./setupModel.js";

/**
 * Loads a 3D model from a given location (URL) and extension
 * @param {Object} params - Parameters object
 * @param {string} params.extension - The extension of the model file, expected to be one of: 'stl', 'obj', 'ply', 'las'
 * @param {string} params.location - The URL location of the model file
 * @returns {Promise<Mesh[]>} A promise that resolves to an array of Three.js Mesh objects
 */
export async function load({ extension, location }) {
  const lcExt = extension.toLowerCase();
  const loader = createLoader(lcExt);

  let loadedObject = await loader.loadAsync(location);

  return setupModel(loadedObject, lcExt);
}
