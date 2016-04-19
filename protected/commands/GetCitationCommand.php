<?php

class GetCitationCommand extends CConsoleCommand {
	public function actionIndex() {
		$limit = 10;
		$offset = 0;

		while(true) {
			$datasets = Dataset::model()->findAll(array("order"=> "identifier ASC", "limit"=>$limit, "offset"=>$offset));
			if(!$datasets)
				break;
			
			foreach($datasets as $dataset) {
				$cite = $dataset->cited;
				$dataset->citation = $cite['total'];
				$dataset->save();
			}

			if(count($datasets) < $limit)
				break;

			$offset = $offset + $limit;
		}
	}
}

?>