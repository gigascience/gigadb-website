import Vue from 'vue'
import { mount } from '@vue/test-utils'
import { eventBus } from '../src/index.js'
import testdata from './helper/db.json'
import SpecifierComponent from '../src/components/SpecifierComponent.vue'

const { uploads } = testdata
const factory = function(options = {}, values = {}) {
    return mount(SpecifierComponent, { //we need mount as we need to render sub components
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
		const examples = [
			{"name":"Luminosity", "value":"400", "unit":"Lux"},
			{"name":"Contrast", "value":"3000", "unit":"Nits"},
		]
		this.renderedComponent = factory({
			Vue,
            attachToDocument: true,
			propsData: {
				fileAttributes: JSON.parse(JSON.stringify( examples )) //we need a copy, not reference
			}
        })
	})
	it('should have a named form for adding a new attribute', function () {
		expect(this.renderedComponent.find('form[name="new-attribute-form"]').exists()).toBe(true)
	})
	it('should have text field for name, value and unit', function () {
		expect(this.renderedComponent.find('input#new-attr-name-field').exists()).toBe(true)
		expect(this.renderedComponent.find('input#new-attr-value-field').exists()).toBe(true)
		expect(this.renderedComponent.find('input#new-attr-unit-field').exists()).toBe(true)
	})
	it('should have an Add attribute button', function () {
		expect(this.renderedComponent.find('button[id="add-new-attribute"]').exists()).toBe(true)
		expect(this.renderedComponent.find('button[id="add-new-attribute"]').text()).toBe("Add")
	})

	it('should display a table of existing attributes', function () {
		const wrapper = this.renderedComponent
		return Vue.nextTick().then(function() {
			expect(wrapper.find('table tbody tr:first-child td:first-child').text()).toBe("Luminosity")
			expect(wrapper.find('table tbody tr:first-child td:nth-child(2)').text()).toBe("400")
			expect(wrapper.find('table tbody tr:first-child td:nth-child(3)').text()).toBe("Lux")
			expect(wrapper.find('table tbody tr:nth-child(2) td:first-child').text()).toBe("Contrast")
			expect(wrapper.find('table tbody tr:nth-child(2) td:nth-child(2)').text()).toBe("3000")
			expect(wrapper.find('table tbody tr:nth-child(2) td:nth-child(3)').text()).toBe("Nits")
			expect(wrapper.find('table tbody tr:nth-child(3)').exists()).toBe(false)
		})
	})

	it('should add new attribute to data array', function () {
		const example = {"name":"Temperature", "value":"45", "unit":"Degree Celsius"}
		const nameField = this.renderedComponent.find('input[id="new-attr-name-field"]')
		const valueField = this.renderedComponent.find('input[id="new-attr-value-field"]')
		const unitField = this.renderedComponent.find('input[id="new-attr-unit-field"]')
		nameField.setValue(example.name)
		valueField.setValue(example.value)
		unitField.setValue(example.unit)
		this.renderedComponent.find('button[id="add-new-attribute"]').trigger('click')
		const wrapper = this.renderedComponent
		return Vue.nextTick().then(function() {
			expect(wrapper.vm.attributes.length).toBe(3)
			expect(wrapper.vm.attributes[2].name).toBe(example.name)
			expect(wrapper.vm.attributes[2].value).toBe(example.value)
			expect(wrapper.vm.attributes[2].unit).toBe(example.unit)
		})
	})

	it('should display delete button next to each attributes', function () {
		const wrapper = this.renderedComponent
		return Vue.nextTick().then(function() {
			expect(wrapper.find('table tbody tr:first-child td:last-child .el-icon-delete').exists()).toBe(true)
			expect(wrapper.find('table tbody tr:nth-child(2) td:last-child .el-icon-delete').exists()).toBe(true)
		})
	})

	it('should trigger attribute deletion upon clicking delete button', function () {
		const wrapper = this.renderedComponent
		return Vue.nextTick().then(function() {
			wrapper.findAll('.el-button--danger').at(1).trigger('click')
			expect(wrapper.vm.attributes.length).toBe(1)
			expect(wrapper.find('table tbody tr:first-child td:first-child').text()).toBe("Luminosity")
		})
	})



})