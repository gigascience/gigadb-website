import Vue from 'vue'
import { shallowMount } from '@vue/test-utils'
import UploaderComponent from '../src/components/UploaderComponent.vue'

import fileData from './helper/base64image.txt' //base64 of CC0_pixel.jpg, use raw-loader
import {eventBus} from '../src/index.js'

const factory = function(options = {}, values = {}) {
    return shallowMount(UploaderComponent, {
        ...options,
        data() {
            return {
                ...values
            }
        }
    })
}

describe('Uploader component', function() {
    let renderedComponent = null

    beforeEach(function () {
        renderedComponent = factory({
                attachToDocument: true,
                propsData: {
                    identifier: '000000',
                    endpoint: '/foobar/',
                },
            }
        )
    })

    it('should load Uppy Dashboard', function() {
        Vue.nextTick().then(function () {
            expect(renderedComponent.find('.uppy-Dashboard-dropFilesTitle').text()).toContain('Drop files here, paste or')
        })
    })

    it('should set value of the dataset hidden text field from props', function() {
        // console.log(renderedComponent.find('#dataset').element.attributes)
        Vue.nextTick().then(function () {
            expect(renderedComponent.find('#dataset').attributes('value')).toBe("000000")
        })
    })

    it('should set TUS endpoint from props', function() {
        // console.log(renderedComponent.vm.uppy.getPlugin('Tus').opts['endpoint'])
        // no need to use Vue.nextTick() here as we are testing instance's variable
        // not the rendered content
        expect(renderedComponent.vm.uppy.getPlugin('Tus').opts['endpoint']).toEqual('/foobar/')
    })

})

describe('Uploader component event handler', function() {
    it('should emit an event when all the uploads have completed', function() {
        const renderedComponent = factory({
                attachToDocument: true,
                propsData: {
                    identifier: '000000',
                    endpoint: '/foobar/',
                },
            }
        )
        let $emitted = false
        eventBus.$on('complete', function($result) {
            $emitted = true //event bus would catch our component's 'complete' event
        })
        renderedComponent.vm.uppy.emit('complete',{}) //force Uppy to emit its 'complete' event
        expect($emitted).toBeTrue()
    })

    it('should emit an event indicating the stage when instanciated', function () {
        let changedTo = ''
        eventBus.$on('stage-changed', function(stage) {
            changedTo = stage
        })
        const renderedComponent = factory({
                attachToDocument: true,
                propsData: {
                    identifier: '000000',
                    endpoint: '/foobar/',
                },
        })
        Vue.nextTick().then(function () {
            expect(changedTo).toEqual('uploading')
        })
    })

    it('should emit an event to indicate it has calculated MD5 checksum for a file', function () {
        let checksumDone = ''
        let notifiedFile = null
        let checksum = "fccbbbfd60e32f2218acc7ae42f325e0"//checksum for the file from cli (md5deep)

        eventBus.$on('checksummed', function(message, file) {
            checksumDone = message
            notifiedFile = file
        })
         const renderedComponent = factory({
                attachToDocument: true,
                propsData: {
                    identifier: '000000',
                    endpoint: '/foobar/',
                },
        })
        const fileReader = new FileReader();
        renderedComponent.vm.uppy.addFile({
          name: 'my-file.txt', // file name
          type: 'text/plain', // file type
          data: new Blob([fileData], {type : 'text/plain'}), // file blob of the image
          meta: {
            // optional, store the directory path of a file so Uppy can tell identical files in different directories apart
            // relativePath: webkitFileSystemEntry.relativePath,
          },
          source: 'Local', // optional, determines the source of the file, for example, Instagram
          isRemote: false // optional, set to true if actual file is not in the browser, but on some remote server, for example, when using companion in combination with Instagram
        })
        return renderedComponent.vm.uppy.upload().then((result) => {
            expect(checksumDone).toEqual('MD5 checksum for my-file.txt done')
            expect(renderedComponent.vm.uppy.getFiles()[0].meta.checksum).toEqual(checksum)
        })  
    })
})