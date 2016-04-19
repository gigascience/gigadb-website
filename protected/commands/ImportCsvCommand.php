<?php
class ImportCsvCommand extends CConsoleCommand {
	public function getHelp() {
		print "
		~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		./yiic importcsv --file= --model= --header=false
		";
	}

	private function importDA($data) {
		foreach($data as $d) {
			$id = $d[0];
			$model = DatasetAuthor::model()->findByPk($d[0]);
			if(!$model) {
				$model = new DatasetAuthor;
				$model->id = $d[0];
			}
			$model->dataset_id = $d[1];
			$model->author_id = $d[2];
			$model->rank = $d[3];
			if(!$model->save())
				echo "Failed to save ".$model->id;
			
		}
	}
	public function actionIndex($file = '', $model = '', $header=true) {
		if(!$file) {
			echo "Please input file\n";
			return 1;
		}
		$file = realpath(dirname(__FILE__).'/../../files/csv/'.$file);
		echo "file is ". $file;

		$data = Utils::readCsv($file);

		if(!$data) {
			echo "File empty \n";
			return 1;
		}
		if($header) {
			$data = array_slice($data, 1);
		}
		switch($model) {
			case 'DatasetAuthor':
				$this->importDA($data);
			break;
			
			default:
			break;
		}


	}
}