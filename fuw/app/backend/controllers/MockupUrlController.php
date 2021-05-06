<?php

namespace backend\controllers;

use yii\rest\ActiveController;

/**
 * REST controller for MockupUrl
 *
 * @uses \common\models\MockupUrl
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 **/
class MockupUrlController extends ActiveController
{
    public $modelClass = 'common\models\MockupUrl';

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
        return $actions;
    }
}
