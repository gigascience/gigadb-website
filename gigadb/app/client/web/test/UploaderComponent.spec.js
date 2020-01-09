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
    it('should load Uppy Dashboard', function() {
        const renderedComponent = factory({
                attachToDocument: true,
                propsData: {
                    identifier: '000000',
                    endpoint: '/foobar/',
                },
            }
        )
        Vue.nextTick().then(function () {
            expect(renderedComponent.find('.uppy-Dashboard-dropFilesTitle').text()).toContain('Drop files here, paste or')
        })
    })

    it('should set value of the dataset hidden text field from props', function() {
        const renderedComponent = factory({
                attachToDocument: true,
                propsData: {
                    identifier: '000000',
                    endpoint: '/foobar/',
                },
            }
        )
        // console.log(renderedComponent.find('#dataset').element.attributes)
        expect(renderedComponent.exists()).toBe(true)
        Vue.nextTick().then(function () {
            expect(renderedComponent.find('#dataset').attributes('value')).toBe("000000")
        })
    })

    it('should set TUS endpoint from props', function() {
        const renderedComponent = factory({
                attachToDocument: true,
                propsData: {
                    identifier: '000000',
                    endpoint: '/foobar/',
                },
            }
        )
        // console.log(renderedComponent.vm.uppy.getPlugin('Tus').opts['endpoint'])
        expect(renderedComponent.vm.uppy.getPlugin('Tus').opts['endpoint']).toEqual('/foobar/')
    })

})

