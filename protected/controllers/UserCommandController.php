<?php

class UserCommandController extends CController
{
    // Members
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

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

			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('claim','cancelClaim'),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
     * Record an authorship claim by a user on the dataset.
     * If claim is recorded successfully, the browser will be redirected to the 'view' page.
     * @param integer $dataset_id, dataset id
     * @param integer $author_id, dataset id
     */
    public function actionClaim($dataset_id, $author_id) {

    	$result['status'] = false;
        $dataset_author = DatasetAuthor::model()->findByAttributes(array('dataset_id' => $dataset_id, 
                                                                        'author_id' => $author_id));


        if( null != $dataset_author ){

            $requester_id = Yii::app()->user->id;
            $actionable_id = $dataset_author->author->id ;
            $action_label = "claim_author";
            $status = "pending";
            $now = new Datetime();

            $claim = new UserCommand;
            $claim->action_label = $action_label;
            $claim->requester_id = $requester_id;
            $claim->actionable_id = $actionable_id;
            $claim->request_date = $now->format(DateTime::ISO8601) ;
            $claim->status = $status ;

			if ($claim->validate('insert')) {

                if ($claim->save(false)) {
                    $result['status'] = true;
                    $result['message'] = "Your claim has been submitted to the administrators.";
                    Yii::log(__FUNCTION__."> created user_command successfully for: ". $claim->requester->id, 'warning');
                }
                else {
                    Yii::log(__FUNCTION__."> create user_command failed", 'warning');
                    $result['status'] = false;
                    $result['message'] = $claim->getErrors();
                }
            }
            else {
                Yii::log(__FUNCTION__."> validation of user_command failed", 'warning');
                $errors = $claim->getErrors();
                // Yii::log(var_dump($errors));
            	if ( isset($errors["requester_id"]) ) {
            		$result['message'] = "We cannot submit the claim: You already have a pending claim.";
            	}
                else {
                	$result['message'] = "We cannot submit the claim: validation problems were encountered.";
                }
                $result['status'] = false;
            }

        }
        else {
            $result['status'] = false;
            $result['message'] = "mismatch between author and dataset.";
        }

        echo json_encode($result);
		Yii::app()->end();

    }

    /**
     * Record an authorship claim by a user on the dataset.
     * If claim is recorded successfully, the browser will be redirected to the 'view' page.
     * @param integer $dataset_id, dataset id
     * @param integer $author_id, dataset id
     */
    public function actionCancelClaim() {

    	$result['status'] = false;
        $claim = UserCommand::model()->findByAttributes( array('requester_id' => Yii::app()->user->id) );

        if( null != $claim ){
	        Yii::log(__FUNCTION__."> deleting record {$claim->id} in user_command ", 'warning');
        	$claim->delete();
        	$result['status'] = true;
            $result['message'] = "Your claim has been successfully canceled.";
        }
        else {
            $result['status'] = false;
            $result['message'] = "You haven't got any current claim.";
        }

        echo json_encode($result);
		Yii::app()->end();

    }

}

?>
