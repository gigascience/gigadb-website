import { createMaterial } from "./material.js";
import { Mesh, Box3, Vector3 } from "three";
import { logger } from "../../../helpers/logger.js";
import { createLoader } from "./loader.js";
/**
 * Loads a 3D model from a given URL and extension
 * @param {Object} params - Parameters object
 * @param {string} params.extension - The extension of the model file, expected to be one of stl, obj, ply, las
 * @param {string} params.url - The URL of the model file
 * @returns {Promise<Mesh>} A promise that resolves to a Three.js Mesh object
 */
export async function load({ extension, url }) {
  logger("info", "Loading model", { extension, url });

  const loader = createLoader(extension);
  logger("info", "Created loader", loader);

  const geometry = await loader.loadAsync(url);
  logger("info", "Loaded geometry", geometry);

  geometry.center();
  logger("info", "Centered geometry");

  const material = createMaterial();
  logger("info", "Created material", material);

  const mesh = new Mesh(geometry, material);
  logger("info", "Created mesh", mesh);

  // Scale model to fit view
  const box = new Box3().setFromObject(mesh);
  logger("info", "Bounding box", box);

  const size = box.getSize(new Vector3()).length();
  logger("info", "Model size", size);

  const scale = 5 / size;
  logger("info", "Calculated scale factor", scale);

  mesh.scale.set(scale, scale, scale);
  logger("info", "Applied scale to mesh", mesh.scale);

  return mesh;
}
