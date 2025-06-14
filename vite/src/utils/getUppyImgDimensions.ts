import type { UppyFile, Meta } from '@uppy/core';

export async function getUppyImgDimensions(imgFile: UppyFile<Meta, Record<string, never>>): Promise<{ width: number; height: number }> {
  return new Promise((resolve, reject) => {
    const url = URL.createObjectURL(imgFile.data);
    const img = new Image();
    img.onload = () => {
      URL.revokeObjectURL(url);
      img.onload = img.onerror = null;
      resolve({ width: img.width, height: img.height });
    };
    img.onerror = (err) => {
      URL.revokeObjectURL(url);
      img.onload = img.onerror = null;
      reject(err instanceof Error ? err : new Error('Failed to load image'));
    };
    img.src = url;
  });
}