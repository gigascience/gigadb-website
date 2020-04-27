import Vue from 'vue'
import { mount } from '@vue/test-utils'
import { eventBus } from '../src/index.js'

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import ElTag from 'element-ui/lib/tag'

Vue.use(ElementUI)

import SamplerComponent from '../src/components/SamplerComponent.vue'

const factory = function(options = {}, values = {}) {
    return mount(SamplerComponent, { //we need mount as we need to render sub components
        ...options,
        data() {
            return {
                ...values
            }
        }
    })
}

describe("Sampler component", function () {
	const sampleList = ["Sample 1", "Sample 2", "Sample 3"]
	it("Load existing sample ids", function () {
		const renderedComponent = factory({
			Vue, propsData: {
				collection: sampleList
			}
		})
		expect(renderedComponent.vm.samples).toContain("Sample 1")	
	})
	it("Add new sample IDs", function () {
		const renderedComponent = factory({
			Vue, propsData: {
				collection: sampleList
			}
		}, {
				inputVisible: true
			})
		const textInput = renderedComponent.find('input[id="new-sample-field"]')

		textInput.setValue("New Sample")
		textInput.trigger('keyup.enter')
		expect(renderedComponent.vm.samples).toContain("New Sample")
	})
	it("Remove sample IDs", function () {
		const sampleList = ["Sample 1", "Sample 2", "Sample 3"]
		const renderedComponent = factory({
			Vue, propsData: {
				collection: sampleList
			}
		})
		const existingSamples = renderedComponent.findAll(ElTag)
		existingSamples.at(1).vm.$emit("close")
		expect(renderedComponent.vm.samples).not.toContain("Sample 2")
	})

	it("emit an event when saving", function () {
		const renderedComponent = factory({
			Vue, propsData: {
				collection: sampleList
			}
		}, {
				inputVisible: true
			})

		let $emitted = false
        eventBus.$on('new-samples-input', function($result) {
            $emitted = true
        })

		const textInput = renderedComponent.find('input[id="new-sample-field"]')
		const saveButton = renderedComponent.find('button[id="save-samples"]')

		textInput.setValue("Sample 4")
		textInput.trigger('keyup.enter')
		saveButton.trigger("click")
		expect($emitted).toBe(true)

	})
})