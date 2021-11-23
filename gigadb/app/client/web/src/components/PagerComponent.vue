<template>
    <nav class="text-right">
        <div class="button-div" v-if="stage === 'uploading' && (uploadsComplete === true || uploadsExist > 0)">
            <a v-bind:href="annotationUrl" class="btn background-btn" style="margin:5px;width:30%">Next (Metadata Form)</a>
        </div>
        <div v-if="stage === 'annotating' && metadataComplete === true">
            <button class="btn background-btn complete" type="submit" style="margin:5px">Complete and return to Your Uploaded Datasets page</button>
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
    props: ["identifier","uploadsExist"],
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