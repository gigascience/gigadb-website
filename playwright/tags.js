// Set tags to true / false to run different subsets of tests
const TAG = {
  // run ALL tests, regardless of any other tags
  RUN_ALL: true,
  //
  // include or exclude tests for specific browsers
  CHROMIUM: true,
  FIREFOX: true,
  WEBKIT: true,
  //
  // include or exclude all tests from a given category
  PUBLIC: true,
  USER: true,
  ADMIN: true,
  //
  // include or exclude specific tags
  DEFAULT: false, // should tag-less tests run?
  1553: false,
  1556: false,
  1558: false,
  1564: false,
  //
  SKIP_DISABLED_ELEMENTS: true,
}

module.exports = TAG