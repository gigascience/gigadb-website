import { STLLoader } from "three/addons/loaders/STLLoader.js";
// import { OBJLoader } from "three/addons/loaders/OBJLoader.js";
// import { PLYLoader } from "three/addons/loaders/PLYLoader.js";

const loaders = {
  stl: STLLoader,
  // obj: OBJLoader,
  // ply: PLYLoader,
};

export function createLoader(extension) {
  const lcExt = extension.toLowerCase();
  if (loaders[lcExt]) {
    return new loaders[lcExt]();
  }

  throw new Error(`Unsupported extension: ${extension}`);
}
