import Vue from 'vue'
import { shallowMount } from '@vue/test-utils'
import PagerComponent from '../src/components/PagerComponent.vue'

const eventBus = new Vue()

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
        renderedComponent = factory({
            propsData: {
                events: eventBus,
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

})