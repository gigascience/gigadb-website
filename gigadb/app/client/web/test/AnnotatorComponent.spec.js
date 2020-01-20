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


    it('should show rows matching the number of uploaded files', function(done) {
        const renderedComponent = factory({
			propsData: {
				identifier: '000000',
				uploads: testdata.uploads
			}
        })
       	renderedComponent.vm.$nextTick(function () {
	       	expect(renderedComponent.findAll('tbody tr').length).toBe(2)
	       	done()
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
		        expect(renderedComponent.find('th')).toContain('File Name')
		        expect(renderedComponent.find('tbody')).toContain('TheProof.csv')
		        expect(renderedComponent.find('tbody')).toContain('TheProof2.jpg')
		        expect(renderedComponent.find('tbody')).not.toContain('foobar.doc')
	        })
        })
    })
    //TODO: should show data type of all upload files
    //TODO: should show format of all upload files
    //TODO: should show size of all upload files
})