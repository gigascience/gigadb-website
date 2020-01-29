import Vue from 'vue'
import DatasetInfoComponent from './components/DatasetInfoComponent.vue'
import UploaderComponent from './components/UploaderComponent.vue'
import PagerComponent from './components/PagerComponent.vue'
import AnnotatorComponent from './components/AnnotatorComponent.vue'

Vue.config.productionTip = false

export const eventBus = new Vue()

new Vue({
	el: '#gigadb-fuw',
	components: {
		'dataset-info': DatasetInfoComponent,
		'uploader': UploaderComponent,
		'pager': PagerComponent,
		'annotator': AnnotatorComponent,
	}
})