import {
  Mesh,
  Box3,
  Vector3,
  BufferGeometry,
  BufferAttribute,
  Points,
} from "three";
import { OBJ, STL, PLY, LAS } from "./extensions.js";
import { createMaterial, createPointsMaterial } from "./material.js";

function hasNormals(geometry) {
  return geometry.attributes && geometry.attributes.normal !== undefined;
}

function setupObj(loadedModel) {
  const meshes = loadedModel.children?.filter((child) => child instanceof Mesh);
  if (!meshes || meshes.length === 0) {
    throw new Error("No meshes found in OBJ file");
  }
  return meshes.flatMap((mesh) => setupGeometry(mesh.geometry));
}

function validateLasModel(loadedModel) {
  if (!loadedModel?.attributes?.POSITION?.value) {
    throw new Error("Invalid LAS file: No position data found");
  }
}

function createPositionAttribute(positionData, vertexCount) {
  const positions = new Float32Array(vertexCount * 3);
  for (let i = 0; i < vertexCount; i++) {
    positions[i * 3] = positionData[i * 3];
    positions[i * 3 + 1] = positionData[i * 3 + 1];
    positions[i * 3 + 2] = positionData[i * 3 + 2];
  }
  return new BufferAttribute(positions, 3);
}

function createColorAttribute(colorData, vertexCount) {
  const colors = new Float32Array(vertexCount * 3);
  for (let i = 0; i < vertexCount; i++) {
    // Convert 8-bit color to float
    colors[i * 3] = colorData[i * 4] / 255;
    colors[i * 3 + 1] = colorData[i * 4 + 1] / 255;
    colors[i * 3 + 2] = colorData[i * 4 + 2] / 255;
  }
  return new BufferAttribute(colors, 3);
}

function centerGeometry(geometry) {
  geometry.computeBoundingBox();
  const center = new Vector3();
  geometry.boundingBox.getCenter(center);
  geometry.translate(-center.x, -center.y, -center.z);
}

function scalePoints(points) {
  const box = new Box3().setFromObject(points);
  const size = box.getSize(new Vector3()).length();
  const scale = 5 / size;
  points.scale.set(scale, scale, scale);
}

function setupLas(loadedModel) {
  validateLasModel(loadedModel);

  const geometry = new BufferGeometry();
  const vertexCount = loadedModel.header.vertexCount;
  const positionData = loadedModel.attributes.POSITION.value;

  geometry.setAttribute(
    "position",
    createPositionAttribute(positionData, vertexCount)
  );

  if (loadedModel.attributes.COLOR_0?.value) {
    const colorData = loadedModel.attributes.COLOR_0.value;
    geometry.setAttribute(
      "color",
      createColorAttribute(colorData, vertexCount)
    );
  }

  const material = createPointsMaterial();
  const points = new Points(geometry, material);

  centerGeometry(geometry);
  scalePoints(points);

  return [points];
}

function setupGeometry(geometry) {
  if (!hasNormals(geometry)) {
    geometry.computeVertexNormals();
  }

  if (geometry.center) {
    geometry.center();
  } else if (geometry.isBufferGeometry) {
    geometry.computeBoundingBox();
    const center = new Vector3();
    geometry.boundingBox.getCenter(center);
    geometry.translate(-center.x, -center.y, -center.z);
  }

  const material = createMaterial();
  const mesh = new Mesh(geometry, material);

  // Scale model to fit view
  const box = new Box3().setFromObject(mesh);
  const size = box.getSize(new Vector3()).length();
  const scale = 5 / size;
  mesh.scale.set(scale, scale, scale);

  return [mesh];
}

// all setup functions return an array of meshes for consistency, even if there is only one mesh
export function setupModel(loadedModel, ext) {
  switch (ext) {
    case OBJ:
      return setupObj(loadedModel);
    case STL:
      return setupGeometry(loadedModel);
    case PLY:
      return setupGeometry(loadedModel);
    case LAS:
      return setupLas(loadedModel);
    default:
      throw new Error(`Unsupported extension: ${ext}`);
  }
}
