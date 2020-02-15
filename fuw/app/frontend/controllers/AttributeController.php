<?php

namespace frontend\controllers;

use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;

/**
 * REST controller for Upload
 *
 * @uses \backend\models\Upload
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 **/
class AttributeController extends ActiveController
{
    public $modelClass = 'common\models\Attribute';

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
                $actions['delete']
            );

        $actions['replace']['class'] = 'frontend\actions\AttributeController\ReplaceAction';

        return $actions;

    }
}
