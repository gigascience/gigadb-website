import { shallowMount } from '@vue/test-utils'
import DatasetInfo from '../src/components/DatasetInfo.vue'

const factory = function (values = {}) {
  return shallowMount(DatasetInfo, {
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
		expect(renderedComponent.find('h4').text()).toContain('GigaDB: Uploading files')
	})

  it('should show DOI passed to the custom element', function () {
    const renderedComponent = shallowMount(DatasetInfo, {
        propsData: {
          identifier: '000000',
        }
      })

    expect(renderedComponent.find('h4').text()).toContain('for the dataset 000000')
  })

})