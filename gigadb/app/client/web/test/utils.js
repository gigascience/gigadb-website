import { shallowMount } from '@vue/test-utils';

const _defaultOptions = {
  attachTo: document.body
};

/**
 * Creates a factory function for mounting Vue components with predefined or custom options.
 *
 * @param {Object} component The Vue component to be mounted.
 * @param {Object} [param={}] Configuration object for the factory, with optional properties:
 *
 *  - mountFnc (Function): The function used for mounting the component (default: shallowMount).
 *
 *  - defaultOptions (Object): Default options applied to the mount function (default: { attachTo: document.body }).
 *
 * @returns {Function} A factory function that takes an object as an argument with possible properties:
 *
 *  - options (Object): Custom options for the mount function, merged with defaultOptions.
 *
 *  - data (Object): Initial data state of the component.
 *
 */
export function makeFactory(component, {
  mountFnc = shallowMount,
  defaultOptions = _defaultOptions
} = {}) {
  return function factory({
    data = {},
    ...options
  } = {}) {
    const _options = {
      ...defaultOptions,
      ...options,
    };

    if (data) {
      _options.data = () => data;
    }

    return mountFnc(component, _options)
  }

}
