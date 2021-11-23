import Vue from 'vue'
Vue.config.productionTip = false

/* start configuration of vue-data-tables */
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
Vue.use(ElementUI)

// set language to EN
import lang from 'element-ui/lib/locale/lang/en'
import locale from 'element-ui/lib/locale'

locale.use(lang)

import { DataTables } from 'vue-data-tables'
Vue.use(DataTables)
/* end configuration of vue-data-tables */

export const eventBus = new Vue()

import DatasetInfoComponent from './components/DatasetInfoComponent.vue'
import UploaderComponent from './components/UploaderComponent.vue'
import PagerComponent from './components/PagerComponent.vue'
import AnnotatorComponent from './components/AnnotatorComponent.vue'
new Vue({
	el: '#gigadb-fuw',
	components: {
		'dataset-info': DatasetInfoComponent,
		'uploader': UploaderComponent,
		'pager': PagerComponent,
		'annotator': AnnotatorComponent,
	}
})