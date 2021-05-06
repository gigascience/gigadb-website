<template>
    <div id="uppy" class="uploader">
        <form id="dataset-metadata-form">
            <input id="dataset" type="hidden" v-bind:value="identifier">
        </form>
        <div class="drag-drop-area"></div>
    </div>
</template>
<style>
</style>
<script>
import Uppy from '@uppy/core'
import Dashboard from '@uppy/dashboard'
import Form from '@uppy/form'
import Tus from '@uppy/tus'
import {Checksum} from '../plugins/uppy-checksum.js'

import {eventBus} from '../index.js'

export default {
	props: ["identifier", "endpoint", "events"],
    data: function() {
        return {
        	uppy:''
        }
    },
    mounted: function() {

    	this.$nextTick(function () {
	    	eventBus.$emit("stage-changed","uploading")
    	})

        this.uppy = new Uppy({
            autoProceed: false,
            debug: false,
        })
        this.uppy.use(Dashboard, {
            inline: true,
            width: 750,
            height: 450,            
            target: '.drag-drop-area',
            hideAfterFinish: true,
            showProgressDetails: true,
            hideUploadButton: false,
            hideRetryButton: false,
            hidePauseResumeButton: false,
            hideCancelButton: false,
            proudlyDisplayPoweredByUppy: false,
            metaFields: [
                { id: 'name', name: 'Name', placeholder: 'file name' },
                { id: 'description', name: 'Description', placeholder: 'describe what the file is about' }
            ],
            locale: {}
        })
        this.uppy.use(Form, {
            target: "#dataset-metadata-form",
            getMetaFromForm: true,
            addResultToForm: false,
            triggerUploadOnSubmit: false,
            submitOnSuccess: false
        })
        this.uppy.use(Tus, { endpoint: this.endpoint })
        this.uppy.use(Checksum, {id: 'Checksum'})
        this.uppy.on('preprocess-progress', (file, data) => {
            eventBus.$emit('checksummed', data.message, file)
        })        
        this.uppy.on('complete', (result) => {
	      	eventBus.$emit('complete',result)
	    })

    },
    beforeDestroy: function () {
    	this.uppy.close()
    }
}
</script>