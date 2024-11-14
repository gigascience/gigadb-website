import { STLLoader } from "three/addons/loaders/STLLoader.js";
import { OBJLoader } from "three/addons/loaders/OBJLoader.js";
import { PLYLoader } from "three/addons/loaders/PLYLoader.js";
import { LASLoader } from "https://cdn.jsdelivr.net/npm/@loaders.gl/las@4.3.2/+esm";
import { load } from "https://cdn.jsdelivr.net/npm/@loaders.gl/core@4.3.2/+esm";
import { STL, OBJ, LAS, PLY } from "./extensions.js";

const loaders = {
  [STL]: STLLoader,
  [OBJ]: OBJLoader,
  [PLY]: PLYLoader,
};

export function createLoader(ext) {
  switch (ext) {
    case STL:
    case OBJ:
    case PLY:
      return new loaders[ext]();
    case LAS:
      return {
        loadAsync: async (location) => {
          const response = await fetch(location);
          const arrayBuffer = await response.arrayBuffer();
          return load(arrayBuffer, LASLoader, {
            shape: "mesh",
            colorDepth: 16,
          });
        },
      };
  }

  throw new Error(`Unsupported extension: ${ext}`);
}
