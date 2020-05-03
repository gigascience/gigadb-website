<?php

namespace backend\controllers;

use yii\rest\ActiveController;

/**
 * REST controller for FiledropAccount
 *
 * @uses \backend\models\FiledropAccount
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 **/
class FiledropAccountController extends ActiveController
{
    public $modelClass = 'backend\models\FiledropAccount';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['create']['class'] = 'backend\actions\FiledropAccountController\CreateAction';
        $actions['delete']['class'] = 'backend\actions\FiledropAccountController\DeleteAction';
        $actions['update']['class'] = 'backend\actions\FiledropAccountController\UpdateAction';
        $actions['move'] = [
            "class" => "backend\actions\FiledropAccountController\MoveFilesAction",
            "modelClass" => $this->modelClass,
        ];
        return $actions;
    }
}
