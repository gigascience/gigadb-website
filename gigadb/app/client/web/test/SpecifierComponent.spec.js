import { enableAutoDestroy, shallowMount } from '@vue/test-utils'
import SpecifierComponent from '../src/components/SpecifierComponent.vue'

import { eventBus } from '../src/index.js'
import testdata from './helper/db.json'

const { uploads } = testdata
const factory = function(options = {}, values = {}) {
    return shallowMount(SpecifierComponent, {
        ...options,
        data() {
            return {
                ...values
            }
        }
    })
}

describe("Specifier component", function () {

	beforeEach(function () {
		this.renderedComponent = factory({
            attachToDocument: true,
			propsData: {
				identifier: '000000',
				uploads: JSON.parse(JSON.stringify( uploads )) //we need a copy, not reference
			}
        })
	})
	it('should have text field for name, value and unit', function () {
		expect(this.renderedComponent.find('input#new-attr-name-field').exists()).toBe(true)
		expect(this.renderedComponent.find('input#new-attr-value-field').exists()).toBe(true)
		expect(this.renderedComponent.find('input#new-attr-unit-field').exists()).toBe(true)
	})
	//TODO: it should have an Add Attribute button
	it('should have an Add attribute button', function () {
		expect(this.renderedComponent.find('[type="submit"').exists()).toBe(true)
		expect(this.renderedComponent.find('[type="submit"').text()).toBe("Add")
	})
	//TODO: it should display a table of existing attributes
})