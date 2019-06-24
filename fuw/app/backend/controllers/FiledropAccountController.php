<?php

namespace backend\controllers;

use yii\rest\ActiveController;

/**
 * REST controller for FiledropAccount
 *
 * POST /filedrop-account/ -> /filedrop-account/create
 * DELETE /filedrop-account/ -> /filedrop-account/close
 * GET /filedrop-account/ -> /filedrop-account/index
 *
 **/
class FiledropAccountController extends ActiveController
{
    public $modelClass = 'backend\models\FiledropAccount';

    public function actionClose()
    {
        return $this->render('close');
    }

    public function actionCreate()
    {
        return $this->render('create');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
