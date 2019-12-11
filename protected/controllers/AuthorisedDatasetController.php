<?php
/**
 * Entry point for operations on dataset requiring user authorisation
 *
 * Currently File upload operations
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class AuthorisedDatasetController extends Controller
{

    /**
     * Filter testing whether user is submitter of dataset in parameter
     *
     * @param CFilterChain $filterChain context of the filter including controller params
     */
    public function filterAuthoriseSubmitter($filterChain)
    {
        $doi = $filterChain->controller->getActionParams()['id'] ?? false ;
        $dataset = Dataset::model()->findByAttributes(["identifier" => $doi]) ?? false ;
        if ($dataset && Yii::app()->user->id === $dataset->submitter_id) {
            $filterChain->run(); // continue with executing further filters and the action
        }
        else {
            throw new CHttpException(403,
                Yii::t('yii','Forbidden: Dataset upload not authorised for user')
            );
        }
    }

    /**
     * Filter testing whether the dataset has right upload status
     *
     * @param CFilterChain $filterChain context of the filter including controller params
     */
    public function filterCheckUploadStatus($filterChain)
    {
        $doi = $filterChain->controller->getActionParams()['id'] ?? false ;
        $dataset = Dataset::model()->findByAttributes(["identifier" => $doi]) ?? false ;
        if ($dataset && 'UserUploadingData' === $dataset->upload_status) {
            $filterChain->run(); // continue with executing further filters and the action
        }
        else {
            throw new CHttpException(409,
                Yii::t('yii','Conflict: Dataset has incorrect status for uploading')
            );
        }
    }
	/**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'authoriseSubmitter', // ensure only submitters can upload files
            'checkUploadStatus', // ensure dataset's right status
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
            array('allow',
                  'actions'=>array('uploadFiles','annotateFiles'),
                  'roles'=>array('user'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * Yii's method for routing urls to an action. Override to use custom actions
     */
    public function actions()
    {
        return array(
            'uploadFiles'=>'application.controllers.authorisedDataset.FilesUploadAction',
            'annotateFiles'=>'application.controllers.authorisedDataset.FilesAnnotateAction',
        );
    }


}
?>