import FileUploader from '../src/components/FileUploader.vue'

import fileData from './helper/base64image.txt' //base64 of CC0_pixel.jpg, use raw-loader
import { eventBus } from '../src/index.js'
import { makeFactory } from './utils.js'

const factory = makeFactory(FileUploader)

describe('FileUploader', function () {
	let wrapper = null

	beforeEach(function () {
		wrapper = factory({
			propsData: {
				identifier: '000000',
				endpoint: '/foobar/',
			},
		})
	})

	xit('should load Uppy Dashboard', function () {
		wrapper.vm.$nextTick().then(function () {
			const dropFilesTitle = wrapper.find('.uppy-Dashboard-AddFiles-title')

			// Error: Expected false to be true.
			expect(dropFilesTitle.exists()).toBeTrue()
			expect(dropFilesTitle.text()).toContain('Drop files here or ')
		})
	})

	it('should set value of the dataset hidden text field from props', function () {
		wrapper.vm.$nextTick().then(function () {
			expect(wrapper.find('#dataset').attributes('value')).toBe('000000')
		})
	})

	it('should set TUS endpoint from props', function () {
		expect(wrapper.vm.uppy.getPlugin('Tus').opts['endpoint']).toEqual(
			'/foobar/'
		)
	})
})

describe('FileUploader event handler', function () {
	it('should emit an event when all the uploads have completed', function () {
		const wrapper = factory({
			attachToDocument: true,
			propsData: {
				identifier: '000000',
				endpoint: '/foobar/',
			},
		})
		let $emitted = false
		eventBus.$on('complete', function ($result) {
			$emitted = true //event bus would catch our component's 'complete' event
		})
		wrapper.vm.uppy.emit('complete', {}) //force Uppy to emit its 'complete' event
		expect($emitted).toBeTrue()
	})

	it('should emit an event to indicate it has calculated MD5 checksum for a file', function () {
		let checksumDone = ''
		let notifiedFile = null
		let checksum = 'fccbbbfd60e32f2218acc7ae42f325e0' //checksum for the file from cli (md5deep)

		eventBus.$on('checksummed', function (message, file) {
			checksumDone = message
			notifiedFile = file
		})
		const wrapper = factory({
			attachToDocument: true,
			propsData: {
				identifier: '000000',
				endpoint: '/foobar/',
			},
		})
		const fileReader = new FileReader()
		wrapper.vm.uppy.addFile({
			name: 'my-file.txt', // file name
			type: 'text/plain', // file type
			data: new Blob([fileData], { type: 'text/plain' }), // file blob of the image
			meta: {
				// optional, store the directory path of a file so Uppy can tell identical files in different directories apart
				// relativePath: webkitFileSystemEntry.relativePath,
			},
			source: 'Local', // optional, determines the source of the file, for example, Instagram
			isRemote: false, // optional, set to true if actual file is not in the browser, but on some remote server, for example, when using companion in combination with Instagram
		})
		return wrapper.vm.uppy.upload().then((result) => {
			expect(checksumDone).toEqual('MD5 checksum for my-file.txt done')
			expect(wrapper.vm.uppy.getFiles()[0].meta.checksum).toEqual(checksum)
		})
	})
})
