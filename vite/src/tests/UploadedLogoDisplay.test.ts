import { describe, test, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import UploadedLogoDisplay from '../components/UploadedLogoDisplay.vue'

describe('UploadedLogoDisplay', () => {
  const defaultImageLocation = '/path/to/logo.jpg'

  const createWrapper = (props = {}) => {
    return mount(UploadedLogoDisplay, {
      props
    })
  }

  test('displays image when uploadedImageLocation is provided', () => {
    const wrapper = createWrapper({
      uploadedImageLocation: defaultImageLocation
    })

    const img = wrapper.find('img')
    expect(img.exists()).toBe(true)
    expect(img.attributes('src')).toBe(defaultImageLocation)
    expect(img.attributes('alt')).toBe('Uploaded Logo')
    expect(wrapper.find('.help-block').exists()).toBe(false)
  })

  test('displays help text when uploadedImageLocation is not provided', () => {
    const wrapper = createWrapper()

    expect(wrapper.find('img').exists()).toBe(false)
    expect(wrapper.find('.help-block').exists()).toBe(true)
    expect(wrapper.find('.help-block').text()).toBe('You did not yet upload a logo')
  })
})