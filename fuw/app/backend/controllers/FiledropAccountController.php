<?php

namespace backend\controllers;

use yii\rest\ActiveController;

/**
 * REST controller for FiledropAccount
 *
 * POST /filedrop-accounts/100001 -> /filedrop-accounts/create/100001
 * DELETE /filedrop-accounts/100001 -> /filedrop-accounts/delete/100001
 * GET /filedrop-accounts/100001 -> /filedrop-accounts/view/100001
 * GET /filedrop-accounts/ -> /filedrop-accounts/
 * @uses \backend\models\FiledropAccount
 *
 **/
class FiledropAccountController extends ActiveController
{
    public $modelClass = 'backend\models\FiledropAccount';

    public function actions()
    {
        $actions = parent::actions();
        $actions['create']['class'] = 'backend\controllers\FiledropAccount\CreateAction';
        return $actions;
    }
}
