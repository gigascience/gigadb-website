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