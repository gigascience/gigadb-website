// Set tags to true / false to run different subsets of tests
const TAG = {
  // run all tests, regardless of any other tags
  RUN_ALL: false,
  //
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
  1553: true,
}

module.exports = TAG