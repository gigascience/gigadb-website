<template>
    <div v-if="stage == 'uploading' && uploadsComplete == true">
        <a href="#" class="btn">Next</a>
    </div>
</template>
<style>
</style>
<script>
export default {
	props: ['events'],
	data: function() {
		return {
			stage: '',
			uploadsComplete: false,
		}
	},
	created: function () {
		this.events.$on('stage-changed', function(stage) {
			this.stage = 'uploading'
		})
		this.events.$on('complete', function(result) {
			this.uploadsComplete = true
		})
	}
}
</script>