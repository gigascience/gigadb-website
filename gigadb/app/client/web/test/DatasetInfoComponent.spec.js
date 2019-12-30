import Vue from 'vue'
import { shallowMount } from '@vue/test-utils'
import DatasetInfoComponent from '../src/components/DatasetInfoComponent.vue'

const factory = function (values = {}) {
  return shallowMount(DatasetInfoComponent, {
    data () {
      return {
        ...values
      }
    }
  })
}

describe('Dataset Info component', function () {
	it('should show the File Upload Wizard stage', function () {
		const renderedComponent = factory()
		expect(renderedComponent.find('.stage-head').text()).toContain('GigaDB: Uploading files')
	})

  it('should show DOI passed to the custom element', function () {
    const renderedComponent = shallowMount(DatasetInfoComponent, {
        propsData: {
          identifier: '000000',
        }
      })

    expect(renderedComponent.find('.stage-head').text()).toContain('for the dataset 000000')
  })

})