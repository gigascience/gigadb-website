<?php
class FixPrefixCommand extends CConsoleCommand {
	
	private function updateSource(&$model) {

		if(strpos($model->url, 'ebi') != false)
			$model->source = 'EBI';

		if(strpos($model->url, 'ncbi') != false)
			$model->source = 'NCBI';

		if(strpos($model->url, 'ddbj') != false)
			$model->source = 'DDBJ';

		$model->save();
	}

	public function actionIndex() {
		$prefixs = Prefix::model()->findAll();
		foreach($prefixs as $prefix) {
			$this->updateSource($prefix);
		}
	}
}

?>