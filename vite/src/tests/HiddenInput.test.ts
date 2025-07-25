import { describe, test, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import HiddenInput from '../components/HiddenInput.vue'

const defaultProps = {
  uploadedImageLocation: '/path/to/image.jpg',
  name: 'hidden_input'
}

const createWrapper = (props = {}) => {
  return mount(HiddenInput, {
    props: {
      ...defaultProps,
      ...props
    }
  })
}

describe('HiddenInput', () => {
  test('renders a hidden input with correct attributes', () => {
    const wrapper = createWrapper()

    const input = wrapper.find('input')
    expect(input.exists()).toBe(true)
    expect(input.attributes('type')).toBe('hidden')
    expect(input.attributes('name')).toBe(defaultProps.name)
    expect(input.attributes('value')).toBe(defaultProps.uploadedImageLocation)
  })

  test('handles null uploadedImageLocation', () => {
    const wrapper = createWrapper({
      uploadedImageLocation: null
    })

    const input = wrapper.find('input')
    expect(input.exists()).toBe(true)
    expect(input.attributes('value')).toBe('')
  })
})