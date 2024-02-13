import Vue from 'vue'
import { shallowMount } from '@vue/test-utils'
import PageNavigation from '../src/components/PageNavigation.vue'

import {eventBus} from '../src/index.js'

const factory = function(options = {}, values = {}) {
    return shallowMount(PageNavigation, {
        ...options,
        data() {
            return {
                ...values
            }
        }
    })
}

describe('Pager component', function() {

    beforeEach(function() {
        this.renderedComponent = factory({
            attachToDocument: true,
            propsData: {
                identifier: '000000',
                uploadsExist: "0",
            }
        })

    })

    it('should show Next button in upload stage when file upload is complete', function() {
        eventBus.$emit('stage-changed', "uploading")
        eventBus.$emit('complete', {})
        const wrapper = this.renderedComponent
        return Vue.nextTick().then(function() {
            expect(wrapper.find('.btn').text()).toEqual('Next (Metadata Form)')
        })
    })

    it('should show Next button if uploads exists serverside', function () {
        eventBus.$emit('stage-changed', "uploading")
        const wrapper = this.renderedComponent
        wrapper.setProps({uploadsExist: 3})
        return Vue.nextTick().then(function() {
            expect(wrapper.find('.btn').text()).toEqual('Next (Metadata Form)')
        })
    })
    it('should not show Next button in upload stage when file upload not complete', function() {
        eventBus.$emit('stage-changed', "uploading")
        const wrapper = this.renderedComponent
        return Vue.nextTick().then(function() {
            expect(wrapper.find('.btn').exists()).toBe(false)
        })
    })

    it('should not show Next button when not in upload stage', function() {
        eventBus.$emit('complete', {})
        const wrapper = this.renderedComponent
        return Vue.nextTick().then(function() {
            expect(wrapper.find('.btn').exists()).toBe(false)
        })
    })

    it('should not show Complete button when metadata form not ready', function () {
        eventBus.$emit('stage-changed', "annotating")
        eventBus.$emit('metadata-ready-status',false)
        const wrapper = this.renderedComponent
        return Vue.nextTick().then(function() {
            expect(wrapper.find('.btn btn-success complete').exists()).toBe(false)
        })
    })

    it('should show Complete button when metadata form is complete', function () {
        eventBus.$emit('stage-changed', "annotating")
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