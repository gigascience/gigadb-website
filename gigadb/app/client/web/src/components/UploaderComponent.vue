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
	props: ["identifier"],
    data: function() {
        return {}
    },
    mounted: function() {

        const uppy = new Uppy({
            autoProceed: true,
            debug: true,
        })
        uppy.use(Dashboard, {
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
        uppy.use(Form, {
            target: "#dataset-metadata-form",
            getMetaFromForm: true,
            addResultToForm: false,
            triggerUploadOnSubmit: false,
            submitOnSuccess: false
        })
        uppy.use(Tus, { endpoint: '/files/' })
    },
}
</script>