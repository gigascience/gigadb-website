import Vue from 'vue'
import { shallowMount } from '@vue/test-utils'
import AnnotatorComponent from '../src/components/AnnotatorComponent.vue'

import { eventBus } from '../src/index.js'

import axios from 'axios'

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


    it('should make a call to the FUW api with identifier parameter', function() {
    	// first get a random string to be our test DOI
    	let array = new Uint32Array(1)
		let doi = window.crypto.getRandomValues(array).toString()
		// add a spy to our http client and let the real method be called
        let instance = axios.create()
        spyOn(instance, 'get')
        // instantiate our component
        const renderedComponent = factory({
			propsData: {
				identifier: doi,
			}
        }, {
        	webclient: instance,
        })
        Vue.nextTick().then(function() {
	        expect(instance.get).toHaveBeenCalledWith('http://gigadb.gigasciencejournal.com:9170/fuw/api/v1/public/upload/', { params: {'filter[doi]': doi}});
	    })
    })
    it('should show rows matching the number of uploaded files', function() {
        const renderedComponent = factory({
			propsData: {
				identifier: '000000',
			}
        }, {
        	apiUrl: 'http://json-server:3000'
        })
        Vue.nextTick().then(function (){
	        renderedComponent.vm.webclient.done(function() {

		        expect(renderedComponent.findAll('tbody tr').length).toBe(2)
	        })
        })
    })
    it('should show file names of all upload files', function() {
        const renderedComponent = factory({
			propsData: {
				identifier: '000000',
			}
        }, {
        	apiUrl: 'http://json-server:3000'
        })
        Vue.nextTick().then(function (){
	        renderedComponent.vm.webclient.done(function() {

		        expect(renderedComponent.find('tbody')).toContain('TheProof.csv')
		        expect(renderedComponent.find('tbody')).toContain('TheProof2.csv')
	        })
        })
    })
    //TODO: should show data type of all upload files
    //TODO: should show format of all upload files
    //TODO: should show size of all upload files
})