import { mount } from '@vue/test-utils'
import AttributesTable from '../src/components/AttributesTable.vue'
import { makeFactory } from './utils.js'
import { setup } from './setup.js'
import { eventBus } from '../src/index.js'

const factory = makeFactory(AttributesTable, {
	mountFnc: mount,
})

setup()

describe('AttributesTable', () => {
	beforeEach(function () {
		jasmine.clock().install()
	})

	afterEach(function () {
		jasmine.clock().uninstall()
		eventBus.$off()
	})

	it('should render a sr-only "attribute added" message when an attribute is added', async function () {
		const wrapper = factory()
		const liveMessage = wrapper.find('.sr-only[aria-live]')

		expect(liveMessage.text()).toBe('')

		wrapper.setProps({
			attributes: [{ name: 'age', value: '12', unit: 'years' }],
		})

		await wrapper.vm.$nextTick()
		jasmine.clock().tick(1000)
		await wrapper.vm.$nextTick()

		expect(liveMessage.text()).toBe('Attribute added')
	})

	it('should render a sr-only "attribute added" message when an attribute is added', async function () {
		const wrapper = factory({
			propsData: {
				attributes: [{ name: 'age', value: '12', unit: 'years' }],
			},
		})
		const liveMessage = wrapper.find('.sr-only[aria-live]')

		expect(liveMessage.text()).toBe('')

		wrapper.setProps({
			attributes: [],
		})

		await wrapper.vm.$nextTick()
		jasmine.clock().tick(1000)
		await wrapper.vm.$nextTick()

		expect(liveMessage.text()).toBe('Attribute removed')
	})

	it("should emit a 'remove-attribute' event when the remove button is clicked", function () {
		const wrapper = factory({
			propsData: {
				attributes: [{ name: 'age', value: '12', unit: 'years' }],
			},
		})
		const deleteButton = wrapper.find(
			'button[aria-label="Remove attribute age"]'
		)

		expect(deleteButton.exists()).toBe(true)

		deleteButton.trigger('click')

		expect(wrapper.emitted()['remove-attribute']).toBeTruthy()
	})
})
