import Vue from 'vue'
import { enableAutoDestroy, mount } from '@vue/test-utils'
import AnnotatorComponent from '../src/components/AnnotatorComponent.vue'

import { eventBus } from '../src/index.js'
import testdata from './helper/db.json'

const { uploads } = testdata
const factory = function(options = {}, values = {}) {
    return mount(AnnotatorComponent, {
        ...options,
        data() {
            return {
                ...values
            }
        }
    })
}

// enableAutoDestroy(afterEach)

describe('Annotator component', function() {

	beforeEach(function () {
		this.renderedComponent = factory({
            attachToDocument: true,
			propsData: {
				identifier: '000000',
				uploads: JSON.parse(JSON.stringify( uploads )) //we need a copy, not reference
			}
        })
	})

    afterEach(function () {
        eventBus.$off()
    })

    it('should show rows matching the number of uploaded files', function() {
        const wrapper = this.renderedComponent
       	this.renderedComponent.vm.$nextTick(function () {
	       	expect(wrapper.findAll('tbody tr').length).toBe(2)
       	})
    })
    it('should show file names of all upload files', function() {
        const wrapper = this.renderedComponent
        return Vue.nextTick().then(function() {
	        expect(wrapper.html()).toContain('File Name')
	        expect(wrapper.html()).toContain('TheProof.csv')
	        expect(wrapper.html()).toContain('TheProof2.jpg')
        })
    })
    it('should show data type of all upload files', function() {
        const wrapper = this.renderedComponent
        return Vue.nextTick().then(function() {
	        expect(wrapper.html()).toContain('Data Type')
	        expect(wrapper.html()).toContain('Text')
	        expect(wrapper.html()).toContain('Image')
        })
    })
   it('should show format of all upload files', function() {
        const wrapper = this.renderedComponent
        return Vue.nextTick().then(function() {
	        expect(wrapper.html()).toContain('Format')
	        expect(wrapper.html()).toContain('TEXT')
	        expect(wrapper.html()).toContain('JPEG')
        })
    })
   it('should show size of all upload files', function() {
        const wrapper = this.renderedComponent
        return Vue.nextTick().then(function() {
	        expect(wrapper.html()).toContain('Size')
	        expect(wrapper.html()).toContain('1120000')
	        expect(wrapper.html()).toContain('1170000')
        })
    })
    it('should set uploads from json in props', function() {
        expect(this.renderedComponent.vm.uploadedFiles).toEqual(testdata.uploads)
    })

    it('should update the metadata for the first upload', function () {
        // Update two fields on the first of the two uploaded files
        const selectField = this.renderedComponent.find('select[id="upload-1-datatype"]')
        selectField.setValue('Rich Text')

        const inputField = this.renderedComponent.find('input[id="upload-1-description"]')
        inputField.setValue('Some description here')

        const wrapper = this.renderedComponent
        return Vue.nextTick().then(function() {
            expect(wrapper.vm.uploadedFiles[0].datatype).toBe('Rich Text')
            expect(wrapper.vm.uploadedFiles[0].description).toBe('Some description here')
            // the other fields remained unchanged
            expect(wrapper.vm.uploadedFiles[0].doi).toBe('000000')
            expect(wrapper.vm.uploadedFiles[0].extension).toBe('TEXT')
        })


    })


    it('should emit a not-ready event when not all fields for all uploads are filled in', function () {
        let $emitted = false
        eventBus.$on('metadata-ready-status', function(status) {
            $emitted = status
        })
        // do stuff here (update fields on both files)
        this.renderedComponent.find('select[id="upload-1-datatype"]').setValue('Rich Text')

        this.renderedComponent.find('input[id="upload-1-description"]').setValue('Some description here')

        this.renderedComponent.find('select[id="upload-2-datatype"]').setValue('Image')

        expect($emitted).toBeFalse()
        expect(this.renderedComponent.vm.isMetadataComplete()).toBeFalse()
    })

    it('should emit a ready event when all fields for all uploads are filled in', function () {
        let $emitted = false
        eventBus.$on('metadata-ready-status', function(status) {
            $emitted = status //event bus would catch our component's 'complete' event
        })
        // do stuff here (update fields on both files)
        this.renderedComponent.find('select[id="upload-1-datatype"]').setValue('Rich Text')

        this.renderedComponent.find('input[id="upload-1-description"]').setValue('Some description here')

        this.renderedComponent.find('select[id="upload-2-datatype"]').setValue('Image')

        this.renderedComponent.find('input[id="upload-2-description"]').setValue('Further details about the thing')

        // as all fields of both files updated, expect the event to have been emitted
        expect($emitted).toBeTrue()
        expect(this.renderedComponent.vm.isMetadataComplete()).toBeTrue()
    })

    it('should take file from uploads and add to delete list when clicking delete', function() {
        const wrapper = this.renderedComponent
        wrapper.findAll(".el-button--danger").at(0).trigger("click")
        return Vue.nextTick().then(function() {
            expect(wrapper.vm.uploadedFiles.length).toBe(1)
            expect(wrapper.vm.filesToDelete.length).toBe(1)
        })
    })

    it('should add hidden text input for each files to delete', function () {
        const wrapper = this.renderedComponent
        wrapper.findAll(".el-button--danger").at(0).trigger("click")
        return Vue.nextTick().then(function() {
            expect(wrapper.findAll('input[type="hidden"]').length).toBe(1)
            wrapper.findAll(".el-button--danger").at(0).trigger("click")
            return Vue.nextTick().then(function() {
                expect(wrapper.findAll('input[type="hidden"]').length).toBe(2)
            })
        })        
    })
})

describe("Annotator component's Attributes button", function () {
    beforeEach(function () {
        this.renderedComponent = factory({
            attachToDocument: true,
            propsData: {
                identifier: '000000',
                uploads: JSON.parse(JSON.stringify( uploads )) //we need a copy, not reference
            }
        })
    })

    afterEach(function () {
        eventBus.$off()
    })

    it('should exist', function () {
        const wrapper = this.renderedComponent
        return Vue.nextTick().then(function() {
            // console.log(wrapper.html())
            expect(wrapper.findAll(".btn.btn-info.btn-small").length).toBe(2)
            expect(wrapper.findAll(".btn.btn-info.btn-small").at(0).text()).toContain("Attributes")
            expect(wrapper.findAll(".btn.btn-info.btn-small").at(1).text()).toContain("Attributes")
        })
    })

    it('should open the drawer for adding attributes', function() {
        const wrapper = this.renderedComponent
        wrapper.findAll(".btn.btn-info.btn-small").at(0).trigger("click")
        return Vue.nextTick().then(function() {
            expect(wrapper.find("#attributes-form").exists()).toBe(true)
        })
    })

    it('should pass the name of the clicked upload to the open drawer', function() {
        const wrapper = this.renderedComponent
        wrapper.findAll(".btn.btn-info.btn-small").at(0).trigger("click")
        return Vue.nextTick().then(function() {
            expect(wrapper.vm.$refs.drawer.title).toBe("Add attributes to file: TheProof.csv")
        })        
    })

    it('should set cursor to clicked upload index and upload Id', function() {
        const wrapper = this.renderedComponent
        wrapper.findAll(".btn.btn-info.btn-small").at(0).trigger("click")
        return Vue.nextTick().then(function() {
            expect(wrapper.vm.drawerIndex).toBe(0)// first upload -> 0
            expect(wrapper.vm.selectedUpload).toBe(1)//first upload has upload.id = 1 in fixtures
        })
    })
})

describe("Annotator component's bulk upload form and instructions", function () {

    beforeEach(function () {
        this.renderedComponent = factory({
            attachToDocument: true,
            propsData: {
                identifier: '000000',
                uploads: JSON.parse(JSON.stringify( uploads )) //we need a copy, not reference
            }
        })
    })

    afterEach(function () {
        eventBus.$off()
    })

    it('should link to example spreadsheet', function () {
        const wrapper = this.renderedComponent
        return Vue.nextTick().then(function() {
            expect(wrapper.find('a[href="/files/examples/bulk-data-upload-example.csv"').exists()).toBe(true)
        })
    })
})