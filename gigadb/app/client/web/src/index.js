import Vue from 'vue'
import DatasetInfoComponent from './components/DatasetInfoComponent.vue'
import UploaderComponent from './components/UploaderComponent.vue'

Vue.config.productionTip = false

new Vue({
	el: '#gigadb-fuw',
	components: {
		'dataset-info': DatasetInfoComponent,
		'uploader': UploaderComponent,
	}
})