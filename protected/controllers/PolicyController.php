<?php

class PolicyController extends CController {

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

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // admin only
				'actions'=>array('create'),
				'roles'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionCreate() {
		$model = new DatasetAttributes;
		$att = Attribute::model()->findByAttributes(array('attribute_name'=> Attribute::FUP));
		if(!$att) {
			$att = new Attribute;
			$att->attribute_name = Attribute::FUP;
			$att->definition = '';
			$att->save();
		}
		$model->attribute_id = $att->id;
		$image = new Images;

		if(isset($_POST['DatasetAttributes'])) {
			$args = $_POST['DatasetAttributes'];
			$exist = DatasetAttributes::model()->findByAttributes(array('dataset_id'=>$args['dataset_id'], 'attribute_id'=>$att->id));
			if($exist)
				$model = $exist;
			$model->attributes = $args;
			$model->value = '';

			//$image->attributes = $_POST['Images'];
			$image->license = "no license";
			$image->photographer = "no author";
			$image->source = "gigadb";
			if($image->validate()) {
				$image->save();
			}
			else {
				Yii::log(print_r($image->getErrors(), true), 'debug');
			}

			if($image) {
				$model->image_id = $image->id;
			}

			if($model->validate()) {
				$model->save();
				$this->redirect('/dataset/'.$model->dataset->identifier);
			}
			else {
				Yii::log(print_r($model->getErrors(), true), 'debug');
			}

		}

		$this->render('create', array('model'=>$model, 'image'=>$image));
	}

}

?>