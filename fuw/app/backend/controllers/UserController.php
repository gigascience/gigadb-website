<?php
namespace backend\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

/**
 * REST controller for User
 *
 * @uses \common\models\User
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 **/
class UserController extends ActiveController
{
    public $modelClass = 'common\models\User';

    /**
     * @inheritdoc
     *
     * We need to JWT based authentication
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

        // disable the "delete" and "update" actions
        unset($actions['delete'], $actions['update']);
        $actions['create']['class'] = 'backend\actions\UserController\CreateAction';

        return $actions;
    }


}
