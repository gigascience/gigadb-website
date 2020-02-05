import Vue from 'vue'
import { shallowMount } from '@vue/test-utils'
import PagerComponent from '../src/components/PagerComponent.vue'

import {eventBus} from '../src/index.js'

const factory = function(options = {}, values = {}) {
    return shallowMount(PagerComponent, {
        ...options,
        data() {
            return {
                ...values
            }
        }
    })
}

describe('Pager component', function() {
    let renderedComponent = null

    beforeEach(function() {
        this.renderedComponent = factory({
            attachToDocument: true,
            propsData: {
                identifier: '000000',
            }
        })

    })

    it('should show Next button in upload stage when file upload is complete', function() {
        eventBus.$emit('state-change', "uploading")
        eventBus.$emit('complete', {})
        Vue.nextTick().then(function() {
            expect(renderedComponent.find('.btn').text()).toEqual('Next')
        })
    })

    it('should not show Next button in upload stage when file upload not complete', function() {
        eventBus.$emit('state-change', "uploading")
        Vue.nextTick().then(function() {
            expect(renderedComponent.find('.btn').exists()).toBe(false)
        })
    })

    it('should not show Next button when not in upload stage', function() {
        Vue.nextTick().then(function() {
            expect(renderedComponent.find('.btn').exists()).toBe(false)
        })
        eventBus.$emit('complete', {})
        Vue.nextTick().then(function() {
            expect(renderedComponent.find('.btn').exists()).toBe(false)
        })
    })

    it('should not show Complete button when metadata form not ready', function () {
        eventBus.$emit('state-changed', "annotating")
        eventBus.$emit('metadata-ready-status',false)
        const wrapper = this.renderedComponent
        return Vue.nextTick().then(function() {
            expect(wrapper.find('.btn btn-success complete').exists()).toBe(false)
        })
    })

    it('should show Complete button when metadata form is complete', function () {
        eventBus.$emit('state-changed', "annotating")
        eventBus.$emit('metadata-ready-status',true)
        // console.log(this.renderedComponent.vm.stage)
        // console.log(this.renderedComponent.vm.metadataComplete)
        const wrapper = this.renderedComponent
        return Vue.nextTick().then(function() {
            // console.log(wrapper.html())
            expect(wrapper.find('.complete').text()).toEqual('Complete and return to Your Uploaded Datasets page')
        })
    })

})