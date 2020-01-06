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

  //TODO: it should set value of the dataset hidden text field to DOI props

  //TODO: it should set TUS endpoint to the endpoint props

  //TODO: it should emit an event when uploads are completed

})