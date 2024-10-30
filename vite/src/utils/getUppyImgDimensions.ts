import type { UppyFile, Meta } from '@uppy/core';

export async function getUppyImgDimensions(imgFile: UppyFile<Meta, Record<string, never>>): Promise<{ width: number; height: number }> {
  return new Promise((resolve, reject) => {
    const url = URL.createObjectURL(imgFile.data);
    const img = new Image();
    img.onload = () => {
      URL.revokeObjectURL(img.src);
      resolve({ width: img.width, height: img.height });
    };
    img.onerror = (error) => {
      URL.revokeObjectURL(img.src);
      reject(error);
    };
    img.src = url;
  });
}