<template>
    <nav>
        <div v-if="stage === 'uploading' && uploadsComplete === true">
            <a href="#" class="btn">Next</a>
        </div>
        <!-- <div v-else>
        	stage: {{ stage }}
        	uploadsComplete: {{ uploadsComplete }}
        </div> -->
    </nav>
</template>
<style>
</style>
<script>

import {eventBus} from '../index.js'

export default {
    data: function() {
        return {
            stage: 'undetermined',
            uploadsComplete: false,
        }
    },
    mounted: function() {
    	const vm = this //see https://stackoverflow.com/a/47148828/6518111
        eventBus.$on('stage-changed', function(stage) {
            vm.stage = stage
        })
        eventBus.$on('complete', function(result) {
            vm.uploadsComplete = true
        })
    }
}
</script>