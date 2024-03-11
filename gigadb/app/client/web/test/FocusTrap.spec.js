import { mount } from '@vue/test-utils'
import { makeFactory } from './utils.js'
import FocusTrap from '../src/components/FocusTrap.vue'

const Parent = {
	components: {
		FocusTrap,
	},
	template: `
    <div>
      <button id="outside" @click="show = true">open</button>
      <FocusTrap ref="focusTrap" v-if="show">
        <div id="container">
          <button id="first">first</button>
          <button id="second">second</button>
          <button id="close" @click="show = false">close</button>
        </div>
      </FocusTrap>
    </div>
  `,
	data() {
		return {
			show: false,
		}
	},
}

const factory = makeFactory(Parent, {
	mountFnc: mount,
})

describe('FocusTrap', () => {
	let wrapper

	beforeEach(() => {
		wrapper = factory()
	})

	afterEach(() => {
		wrapper.destroy()
	})

	it('moves focus within after toggling', async () => {
		expect(wrapper.findComponent(FocusTrap).exists()).toBe(false)
		const toggleButton = wrapper.find('#outside')
		await toggleButton.trigger('click')

		expect(wrapper.findComponent(FocusTrap).exists()).toBe(true)

		expect(document.activeElement.id).toBe('first')
	})

	it('Newly added elements are added to the list of focusable elements', async () => {
		await wrapper.find('#outside').trigger('click')

    expect(wrapper.vm.$refs.focusTrap.focusableElements.length).toBe(3)

		await wrapper
			.find('#container')
			.element.appendChild(document.createElement('button'))

		expect(wrapper.vm.$refs.focusTrap.focusableElements.length).toBe(4)
	})

  it('Removed elements are removed to the list of focusable elements', async () => {
		await wrapper.find('#outside').trigger('click')

    expect(wrapper.vm.$refs.focusTrap.focusableElements.length).toBe(3)

    const container = wrapper.find('#container').element

		await container.removeChild(wrapper.find("#first").element)

		expect(wrapper.vm.$refs.focusTrap.focusableElements.length).toBe(2)
	})

	it('return focus to previously focused element', async () => {
		const outsideBtn = wrapper.find('#outside')

		outsideBtn.element.focus()
		await outsideBtn.trigger('click')

		expect(document.activeElement.id).toBe('first')

		await wrapper.find('#close').trigger('click')

		expect(document.activeElement.id).toBe('outside')
	})
})
