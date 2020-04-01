<?php

namespace frontend\controllers;

use Yii;
use yii\rest\ActiveController;
use common\models\MockupUrl;

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
        // $behaviors['authenticator'] = [
        //     'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
        // ];
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
        $actions['view']['class'] = [$this,'actionShow'];
        return $actions;
    }

    public function actionShow($url_fragment)
    {
        Yii::info("getting MockupUrl from url_fragment: $url_fragment");
        $model = MockupUrl::findOne(["url_fragment" => $url_fragment]);
        return $model;
    }
}
