<?php

class ReportController extends Controller
{
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules()
	{
		return array(
			array('allow', // admin only
				'actions'=>array('index'),
				'roles'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function format_date($from, $to) {
		$f = lcfirst(strftime('%b%d', strtotime($from)));
		$t = lcfirst(strftime('%b%d', strtotime($to)));
		return $f.'-'.$t;
	}

	public function groupData($data, $num = 0) {
		$result = array();
		$max = count($data);
		//Yii::log(print_r($data, true), 'debug');
		if($num == 0) {
			foreach($data as $date) {
				$l = array();
				$l[] = lcfirst(strftime('%b%d', strtotime($date[0])));
				$l[] = intval($date[1]);
				$l[] = intval($date[2]);
				$l[] = intval($date[3]);
				$result[] = $l;
			}
		} else {
			for($i = 0; $i < $max; $i++) {

				$next = $i + $num >= $max ? $max -1 : $i + $num;

				$l = array($this->format_date($data[$i][0], $data[$next][0]));
				
				$visit = 0;
				$visitor = 0;
				$view = 0;

				for($j = $i; $j < $next; $j++) {
					$visit += $data[$j][1];
					$visitor += $data[$j][2];
					$view += $data[$j][3];
				}
				$l[] = $visit;
				$l[] = $visitor;
				$l[] = $view;
				$result[] = $l;

				$i = $next;
			}
		}
		//Yii::log(print_r($result, true), 'debug');
		return $result;
	}

	public function getDateRange($to, $from) {
		$diff = (abs(strtotime($from) - strtotime($to)))/(60*60*24);
		if($diff >= 180) {
			return 29;
		} else if($diff >= 14) {
			return 6;
		} else {
			return 0;
		}
	}

	public function actionIndex()
	{
		$dois = Util::getDois();
		$l = array();
		$l['all'] = Yii::t('app','All DOIs');
		foreach($dois as $doi) {
			$l[$doi['identifier']] = $doi['identifier'];
		}

		//Yii::log(print_r($l, true), 'debug');
		
		$args = array();
		$data = array();
		$selectDois = array();
		if(isset($_POST['Report'])) {
			$args = $_POST['Report'];
			if($args['start_date'] && $args['ids']) {
				$paths = array();
				if(in_array('all', $args['ids'])) {
					array_push($paths, '=~^/dataset/','=~^/dataset/view/id/');
					$selectDois = 'all';
				} else {
					$selectDois = $args['ids'];
					foreach($selectDois as $selectDoi) {
						array_push($paths,'==/dataset/'.$selectDoi,'==/dataset/view/id/'.$selectDoi);
					}
				}

				if(!$args['end_date']) {
					$args['end_date'] =  date('Y-m-d');
				}

				$dateRange = $this->getDateRange($args['start_date'],$args['end_date']);

				$analytics = Yii::app()->analytics;
				$data = $analytics->getAnalyticsData($args['start_date'], $args['end_date'], $paths);
				$data = $this->groupData($data, $dateRange);
			}
		}

		$this->render('index', array('dois'=>$l, 'args'=>$args, 'linedata'=>CJSON::encode($data), 'selectDois'=>$selectDois));
	}
}