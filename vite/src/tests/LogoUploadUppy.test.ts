import { test, expect, describe, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import LogoUploadUppy from '../components/LogoUploadUppy.vue'
import { nextTick } from 'vue'

const createEventEmitter = () => {
  const listeners: Record<string, Function[]> = {}
  return {
    on: vi.fn((event: string, callback: Function) => {
      listeners[event] = listeners[event] || []
      listeners[event].push(callback)
    }),
    emit: vi.fn((event: string, ...args: any[]) => {
      if (listeners[event]) {
        listeners[event].forEach(callback => callback(...args))
      }
    }),
    getPlugin: vi.fn(() => ({
      openFileEditor: vi.fn()
    }))
  }
}

vi.mock('@uppy/core', () => ({
  default: vi.fn().mockImplementation(() => {
    const emitter = createEventEmitter()
    return {
      ...emitter,
      use: vi.fn().mockReturnThis(),
    }
  })
}))

const defaultProps = {
  endpoint: '/api/upload',
  imageLocation: null,
  hiddenInputName: 'logo_image'
}

const createMockFile = (overrides = {}) => ({
  id: 'test-file-1',
  name: 'test.jpg',
  type: 'image/jpeg',
  data: new Blob(),
  size: 1234,
  progress: {
    percentage: 0,
    bytesUploaded: 0,
    bytesTotal: 1234
  },
  ...overrides
})

const createWrapper = (props = {}) => {
  return mount(LogoUploadUppy, {
    props: {
      ...defaultProps,
      ...props
    }
  })
}

describe('LogoUploadUppy', () => {
  test('renders properly with default props', () => {
    const wrapper = createWrapper()
    expect(wrapper.find('.uppy-dashboard-wrapper').exists()).toBe(true)
  })

  test('shows hidden input only when hiddenInputName is provided', async () => {
    const wrapper = createWrapper({
      hiddenInputName: undefined
    })

    expect(wrapper.find('input[type="hidden"]').exists()).toBe(false)

    await wrapper.setProps({ hiddenInputName: 'test_input' })
    expect(wrapper.find('input[type="hidden"]').exists()).toBe(true)
  })

  test('updates uploadedImageLocation when image is successfully uploaded', async () => {
    const wrapper = createWrapper()

    const vm = wrapper.vm as any
    const response = {
      body: {
        success: true,
        image_location: '/uploads/test.jpg'
      }
    }

    vm.uppy.emit('upload-success', {}, response)
    await nextTick()

    expect(wrapper.emitted()).toBeTruthy()
    expect(vm.uploadedImageLocation).toBe('/uploads/test.jpg')
  })

  test('handles error states correctly', async () => {
    const wrapper = createWrapper()

    const vm = wrapper.vm as any

    vm.uppy.emit('upload-error', null, null, null)
    await nextTick()
    expect(vm.errorMessage).toBe('Failed to upload file. Please try again.')

    const errorResponse = JSON.stringify({ message: 'Custom error message' })
    vm.uppy.emit('upload-error', null, null, errorResponse)
    await nextTick()
    expect(vm.errorMessage).toBe('Custom error message')
  })

  test('manages focus states correctly', async () => {
    const wrapper = createWrapper()

    const vm = wrapper.vm as any
    expect(vm.isWrapperFocusable).toBe(true)

    const mockFile = createMockFile()

    vm.uppy.emit('file-added', mockFile)
    await nextTick()
    expect(vm.isWrapperFocusable).toBe(false)

    vm.uppy.emit('file-removed', mockFile)
    await nextTick()
    expect(vm.isWrapperFocusable).toBe(true)
  })
})
