import Vue from 'vue'
import { shallowMount } from '@vue/test-utils'
import UploaderComponent from '../src/components/UploaderComponent.vue'

const factory = function (values = {}) {
  return shallowMount(UploaderComponent, {
    attachToDocument: true,
    data () {
      return {
        ...values
      }
    }
  })
}

describe('Uploader component', function () {
	it('should load Uppy Dashboard', function () {
		const renderedComponent = factory()
		expect(renderedComponent.find('.uppy-Dashboard-dropFilesTitle').text()).toContain('Drop files here, paste or')
	})

  it('should set value of the dataset hidden text field from props', function () {
        const renderedComponent = shallowMount(UploaderComponent, {
          // attachToDocument: true,
          propsData: {
            identifier: '000000',
            endpoint: '/foobar/',
          },
          data: function () {
            return {
              uppy: '',
            }
          }
        })
        // console.log(renderedComponent.find('#dataset').element.attributes)
        expect(renderedComponent.find('#dataset').attributes('value')).toBe("000000")
  })

  it('should set TUS endpoint from props', function () {
        const renderedComponent = shallowMount(UploaderComponent, {
          // attachToDocument: true,
          propsData: {
            identifier: '000000',
            endpoint: '/foobar/',
          },
          data: function () {
            return {
              uppy: '',
            }
          }
        })
        // console.log(renderedComponent.vm.uppy.getPlugin('Tus').opts['endpoint'])
        expect(renderedComponent.vm.uppy.getPlugin('Tus').opts['endpoint']).toEqual('/foobar/')
  })

  //TODO: it should emit an event when uploads are completed

})