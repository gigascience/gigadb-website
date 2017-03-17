<?php
/**
 * Created by PhpStorm.
 * User: rija
 * Date: 09/02/2017
 * Time: 15:09
 */




class FileController extends Controller
{
    /**
    * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
    * using two-column layout. See 'protected/views/layouts/column2.php'.
    */
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

            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions'=>array('Bundle'),
                'users'=>array('*'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }


    public function actionBundle($operation) {

        $result = array('status' => "ERROR");
        $location = Yii::app()->request->getParam('location');

        //init session bundle
        if(!isset(Yii::app()->session['bundle'])){
          Yii::app()->session['bundle'] = serialize(array());
        }


        if ($operation === 'addToBundle') {


            //add new item to session myvar
            $bundle = unserialize(Yii::app()->session['bundle']);
            $bundle[$location] = 1 ;
            Yii::app()->session['bundle'] = serialize($bundle);
            $result["status"] = "OK";
            $result["lastop"] = "addToBundle";
        }
        else if ($operation === 'removeFromBundle') {
            $bundle = unserialize(Yii::app()->session['bundle']);
            unset($bundle[$location]);
            Yii::app()->session['bundle'] = serialize($bundle);
            $result["status"] = "OK";
            $result["lastop"] = "removeFromBundle";
        }
        else {
            $result["status"] = "ERROR: Unrecognised operation";
        }

        echo json_encode($result);
        // Yii::app()->session[$location] = 1;
        // echo Yii::app()->session[$location]; // Prints "value"
        //unset(Yii::app()->session[$location]);

        //var_dump(Yii::app()->session->keys);
		Yii::app()->end();

    }


}
