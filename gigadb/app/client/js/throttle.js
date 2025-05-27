// Creates a throttled version of the given function that only invokes the function at most once every specified wait period.
//
// @param {Function} func - The function to throttle.
// @param {number} wait - The number of milliseconds to throttle invocations to.
// @returns {Function} A throttled version of the input function.
export function throttle(func, wait) {
  let timeout;
  let lastArgs;
  return function (...args) {
    lastArgs = args;
    if (!timeout) {
      func.apply(this, args);
      timeout = setTimeout(() => {
        timeout = null;
        if (lastArgs) {
          func.apply(this, lastArgs);
          lastArgs = null;
        }
      }, wait);
    }
  };
}
