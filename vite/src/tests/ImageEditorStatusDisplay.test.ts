import { describe, test, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import ImageEditorStatusDisplay from '../components/ImageEditorStatusDisplay.vue'
import { config } from '../config'

const { maxHeight } = config

describe('ImageEditorStatusDisplay', () => {
  const createWrapper = (props = {}) => {
    return mount(ImageEditorStatusDisplay, {
      props: {
        imageDimensions: { width: 800, height: 600 },
        ...props
      }
    })
  }

  test('renders current dimensions correctly', () => {
    const wrapper = createWrapper({
      imageDimensions: { width: 1024, height: 768 }
    })

    expect(wrapper.text()).toContain('Current dimensions: 1024px × 768px')
  })

  test('does not render when dimensions are zero', () => {
    const wrapper = createWrapper({
      imageDimensions: { width: 0, height: 0 }
    })

    expect(wrapper.find('.status-display').exists()).toBe(false)
  })

  test('shows error when height exceeds maximum', () => {
    const wrapper = createWrapper({
      imageDimensions: { width: 800, height: maxHeight + 100 }
    })

    const errorElement = wrapper.find('.status-error')
    expect(errorElement.exists()).toBe(true)
    expect(errorElement.text()).toContain(`Height exceeds ${maxHeight}px limit`)
  })

  test('does not show error when height is within limit', () => {
    const wrapper = createWrapper({
      imageDimensions: { width: 800, height: maxHeight - 100 }
    })

    expect(wrapper.find('.status-error').exists()).toBe(false)
  })

  test('shows correct screen reader messages when height exceeds limit', () => {
    const wrapper = createWrapper({
      imageDimensions: { width: 800, height: maxHeight + 100 }
    })

    const srError = wrapper.find('.sr-only')
    expect(srError.exists()).toBe(true)
    expect(srError.text()).toContain(`Image height exceeds ${maxHeight} pixels`)
  })

  test('shows correct screen reader messages when height is within limit', () => {
    const wrapper = createWrapper({
      imageDimensions: { width: 800, height: maxHeight - 100 }
    })

    const srSuccess = wrapper.find('.sr-only')
    expect(srSuccess.exists()).toBe(true)
    expect(srSuccess.text()).toBe('Image height is valid')
  })

  test('handles edge case at exact maxHeight', () => {
    const wrapper = createWrapper({
      imageDimensions: { width: 800, height: maxHeight }
    })

    expect(wrapper.find('.status-error').exists()).toBe(false)
    expect(wrapper.find('.sr-only').text()).toBe('Image height is valid')
  })
})