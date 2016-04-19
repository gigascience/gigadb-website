<?php

class CallbackController extends Controller {

	public function actionIndex() {
		var_dump(session_id());
		var_dump($_SESSION);
		//var_dump(Yii::app()->user);
	}

}
