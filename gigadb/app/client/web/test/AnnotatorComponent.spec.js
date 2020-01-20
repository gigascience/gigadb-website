import Vue from 'vue'
import { shallowMount } from '@vue/test-utils'
import AnnotatorComponent from '../src/components/AnnotatorComponent.vue'

import { eventBus } from '../src/index.js'
import testdata from './helper/db.json'

const factory = function(options = {}, values = {}) {
    return shallowMount(AnnotatorComponent, {
        ...options,
        data() {
            return {
                ...values
            }
        }
    })
}

describe('Annotator component', function() {


    it('should show rows matching the number of uploaded files', function() {
        const renderedComponent = factory({
			propsData: {
				identifier: '000000',
				uploads: testdata.uploads
			}
        })
       	renderedComponent.vm.$nextTick(function () {
	       	expect(renderedComponent.findAll('tbody tr').length).toBe(2)
       	})
    })
    it('should show file names of all upload files', function() {
        const renderedComponent = factory({
			propsData: {
				identifier: '000000',
				uploads: testdata.uploads
			}
        })
        renderedComponent.vm.$nextTick(function () {
	        expect(renderedComponent.html()).toContain('File Name')
	        expect(renderedComponent.html()).toContain('TheProof.csv')
	        expect(renderedComponent.html()).toContain('TheProof2.jpg')
        })
    })
    //TODO: should show data type of all upload files
    //TODO: should show format of all upload files
    //TODO: should show size of all upload files
})