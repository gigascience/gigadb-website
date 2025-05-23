export function coerceSelected(value) {
  if (typeof value === 'string') {
    const parsed = parseInt(value, 10);
    return isNaN(parsed) ? value : parsed;
  }
  return value;
}
