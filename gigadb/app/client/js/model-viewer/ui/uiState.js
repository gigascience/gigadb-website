export function createUiState(initialState, onStateChange) {
  const state = { ...initialState };
  return new Proxy(state, {
    set(target, property, value) {
      target[property] = value;
      onStateChange(property, value);
      return true;
    },
  });
}
