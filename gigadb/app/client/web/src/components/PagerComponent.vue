<template>
    <nav>
        <div v-if="stage === 'uploading' && uploadsComplete === true">
            <a v-bind:href="annotationUrl" class="btn">Next</a>
        </div>
        <div v-if="stage === 'annotating' && metadataComplete === true">
            <a href="#" class="btn btn-success complete">Complete and return to Your Uploaded Datasets page</a>
        </div>
<!--         <div v-else>
        	stage: {{ stage }}
        	metadataComplete: {{ metadataComplete }}
        </div> -->
    </nav>
</template>
<style>
</style>
<script>

import {eventBus} from '../index.js'

export default {
    props: ["identifier"],
    data: function() {
        return {
            stage: 'undetermined',
            uploadsComplete: false,
            metadataComplete: false,
            annotationUrl: '/authorisedDataset/annotateFiles/id/'+this.identifier
        }
    },
    mounted: function() {
    	const vm = this //see https://stackoverflow.com/a/47148828/6518111
        eventBus.$on('stage-changed', function(stage) {
            vm.stage = stage
            // console.log('Pager, stage changed to ' + vm.stage)
        })
        eventBus.$on('complete', function(result) {
            vm.uploadsComplete = true
            // console.log('Pager, uploadsComplete set to true')
        })
        eventBus.$on('metadata-ready-status', function(status) {
            vm.metadataComplete = status
            // console.log('Pager, metadataComplete set to ' + vm.metadataComplete)
        })
    }
}
</script>