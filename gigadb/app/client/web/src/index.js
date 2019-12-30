import Vue from 'vue'
import DatasetInfoComponent from './components/DatasetInfoComponent.vue'

Vue.config.productionTip = false

new Vue({
	el: '#gigadb-fuw',
	components: {
		'dataset-info': DatasetInfoComponent,
	}
})