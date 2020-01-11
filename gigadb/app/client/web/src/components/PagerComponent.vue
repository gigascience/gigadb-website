<template>
    <nav>
        <div v-if="stage === 'uploading' && uploadsComplete === true">
            <a href="#" class="btn">Next</a>
        </div>
    </nav>
</template>
<style>
</style>
<script>

import {eventBus} from '../index.js'

export default {
    props: ['events'],
    data: function() {
        return {
            stage: '',
            uploadsComplete: false,
        }
    },
    created: function() {
        eventBus.$on('stage-changed', function(stage) {
            this.stage = stage
        })
        eventBus.$on('complete', function(result) {
            this.uploadsComplete = true
        })
    }
}
</script>