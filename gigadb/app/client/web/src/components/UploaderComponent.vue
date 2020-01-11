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

export default {
	props: ["identifier", "endpoint", "events"],
    data: function() {
        return {
        	uppy:''
        }
    },
    mounted: function() {

    	if (typeof this.events !== 'undefined') {
	    	this.events.$emit("stage-changed","uploading")
	    }

        this.uppy = new Uppy({
            autoProceed: false,
            debug: false,
        })
        this.uppy.use(Dashboard, {
            inline: true,
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
        this.uppy.on('complete', (result) => {
	      	this.events.$emit('complete',result)
	    })

    },
    beforeDestroy: function () {
    	this.uppy.close()
    }
}
</script>