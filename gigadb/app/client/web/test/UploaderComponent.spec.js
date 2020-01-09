import Vue from 'vue'
import { shallowMount } from '@vue/test-utils'
import UploaderComponent from '../src/components/UploaderComponent.vue'

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
        let result = {}
        renderedComponent.vm.emitOnComplete(result)
        expect(renderedComponent.emitted().complete).toBeTruthy()
        // it's not worth the cost to also test that our emitOnComplete method
        // is called by Uppy event handler, as we can test that in integration
        // and acceptance tests.
    })
})