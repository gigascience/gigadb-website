const maxHeight = 60;
const maxSize = 1000000;
const maxSizeMb = maxSize / 1e6;

export const config = Object.freeze({
  maxHeight,
  maxSize,
  maxSizeMb
} as const)