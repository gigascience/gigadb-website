import { STLLoader } from "three/addons/loaders/STLLoader.js";
import { createMaterial } from "./material.js";
import { Mesh, Box3, Vector3 } from "three";
import { logger } from "../../../helpers/logger.js";

export async function load({ extension, url }) {
  logger('info', 'Loading model', { extension, url });

  const loader = new STLLoader();
  logger('info', 'Created STL loader');

  try {
    const geometry = await loader.loadAsync(url);
    logger('info', 'Loaded geometry', geometry);

    geometry.center();
    logger('info', 'Centered geometry');

    const material = createMaterial();
    logger('info', 'Created material', material);

    const mesh = new Mesh(geometry, material);
    logger('info', 'Created mesh', mesh);

    // Scale model to fit view
    const box = new Box3().setFromObject(mesh);
    logger('info', 'Bounding box', box);

    const size = box.getSize(new Vector3()).length();
    logger('info', 'Model size', size);

    const scale = 5 / size;
    logger('info', 'Calculated scale factor', scale);

    mesh.scale.set(scale, scale, scale);
    logger('info', 'Applied scale to mesh', mesh.scale);

    return mesh;
  } catch (error) {
    logger('error', 'Error loading model', error);
    throw error;
  }
}
