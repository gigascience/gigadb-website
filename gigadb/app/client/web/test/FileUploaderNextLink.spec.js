import FileUploaderNextLink from '../src/components/FileUploaderNextLink.vue'
import { makeFactory } from './utils.js'
import { eventBus } from '../src/index.js'

const factory = makeFactory(FileUploaderNextLink)

describe('FileUploaderNextLink', () => {
	afterEach(() => {
		eventBus.$off()
	})

	it('Link is disabled if no uploads exist', () => {
		const wrapper = factory({
			propsData: {
				uploadsExist: '0',
				identifier: '1234567',
			},
		})
		const link = wrapper.find('a')
		expect(link.attributes('aria-disabled')).toBe('true')
	})
	it('Link is enabled if uploads exist', () => {
		const wrapper = factory({
			propsData: {
				uploadsExist: '1',
				identifier: '1234567',
			},
		})
		const link = wrapper.find('a')
		expect(link.attributes('aria-disabled')).toBe('false')
	})
	it('on complete event, link is enabled', async () => {
		const wrapper = factory({
			propsData: {
				uploadsExist: '0',
				identifier: '1234567',
			},
		})
		const link = wrapper.find('a')
		eventBus.$emit('complete')
		await wrapper.vm.$nextTick()
		expect(link.attributes('aria-disabled')).toBe('false')
	})
})
