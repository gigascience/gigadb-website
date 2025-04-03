/**
 * Creates a debounced version of a function that delays invoking the function until after
 * a specified wait time has elapsed since the last time it was invoked.
 * Useful for rate-limiting function calls that would otherwise be called too frequently.
 *
 * @param {Function} func The function to debounce
 * @param {number} wait The number of milliseconds to delay
 * @returns {Function} Returns the debounced function with a cancel() method
 * @example
 *
 * // Create debounced version of expensive calculation
 * const debouncedCalc = debounce(expensiveCalculation, 250);
 *
 * // Call it multiple times rapidly
 * debouncedCalc(); // Only the last call after 250ms of inactivity executes
 * debouncedCalc();
 * debouncedCalc();
 *
 * // Cancel pending execution if needed
 * debouncedCalc.cancel();
 */
export function debounce(func, wait) {
  let timeout;
  const debouncedFn = function (...args) {
    const context = this;
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(() => {
      timeout = null;
      func.apply(context, args);
    }, wait);
  };
  debouncedFn.cancel = function () {
    if (timeout) {
      clearTimeout(timeout);
      timeout = null;
    }
  };
  return debouncedFn;
}
