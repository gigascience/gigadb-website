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
	let renderedComponent
	beforeEach(function () {
		renderedComponent = factory({
			propsData: {
				identifier: '000000',
				uploads: testdata.uploads
			}
        })
	})
    it('should show rows matching the number of uploaded files', function() {
       	renderedComponent.vm.$nextTick(function () {
	       	expect(renderedComponent.findAll('tbody tr').length).toBe(2)
       	})
    })
    it('should show file names of all upload files', function() {
        renderedComponent.vm.$nextTick(function () {
	        expect(renderedComponent.html()).toContain('File Name')
	        expect(renderedComponent.html()).toContain('TheProof.csv')
	        expect(renderedComponent.html()).toContain('TheProof2.jpg')
        })
    })
    it('should show data type of all upload files', function() {
        renderedComponent.vm.$nextTick(function () {
	        expect(renderedComponent.html()).toContain('Data Type')
	        expect(renderedComponent.html()).toContain('Text')
	        expect(renderedComponent.html()).toContain('Image')
        })
    })
   it('should show format of all upload files', function() {
        renderedComponent.vm.$nextTick(function () {
	        expect(renderedComponent.html()).toContain('Format')
	        expect(renderedComponent.html()).toContain('TEXT')
	        expect(renderedComponent.html()).toContain('JPEG')
        })
    })
   it('should show size of all upload files', function() {
        renderedComponent.vm.$nextTick(function () {
	        expect(renderedComponent.html()).toContain('Size')
	        expect(renderedComponent.html()).toContain('1120000')
	        expect(renderedComponent.html()).toContain('1170000')
        })
    })
    it('should set uploads from json in props', function() {
        expect(renderedComponent.vm.uploadedFiles).toEqual(testdata.uploads)
    })
})