import Vue from 'vue'
import { shallowMount } from '@vue/test-utils'
import AnnotatorComponent from '../src/components/AnnotatorComponent.vue'

import { eventBus } from '../src/index.js'

import axios from 'axios'
import moxios from 'moxios'

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

    beforeEach(function() {
        moxios.install()
    })
    afterEach(function() {
        moxios.uninstall()
    })

    it('should show rows matching the number of uploaded files', function() {
        spyOn(axios, 'get').and.callThrough()
        const renderedComponent = factory({
			propsData: {
				identifier: '000000',
			}
        })
        expect(axios.get).toHaveBeenCalledWith('http://gigadb.gigasciencejournal:9170/fuw/api/v1/public/upload?doi=000000');
        moxios.wait(function() {
            let request = moxios.requests.mostRecent()
            request.respondWith({
                status: 200,
                response: {
                    uploads: [
                        { id: 1, doi: '000000', FileName: 'TheProof.csv', DataType: 'Text', Format: 'TEXT', Size: '112KiB' },
                        { id: 2, doi: '000000', FileName: 'TheProof2.csv', DataType: 'Text', Format: 'TEXT', Size: '112KiB' },
                    ]
                }
            }).then(function() {
                expect(renderedComponent.findAll('tbody tr').length).toBe(2)
                done()
            })
        })
    })
    //TODO: should show file names of all upload files
    //TODO: should show data type of all upload files
    //TODO: should show format of all upload files
    //TODO: should show size of all upload files
})