<?php

namespace frontend\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;

/**
 * REST controller to access notification mechanisms (email for now)
 * *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 **/
class NotificationController extends ActiveController
{
    public $modelClass = 'common\models\Upload';

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
        unset(
                $actions['create'],
                $actions['update'],
                $actions['view'],
                $actions['index'],
                $actions['delete']
            );
        $actions['emailSend'] = [
            'class' => 'frontend\actions\NotificationController\EmailSendAction'
        ];
    return $actions;

    }
}
