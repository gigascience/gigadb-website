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
                $actions['update'],
                $actions['delete']
            );


        $actions['create'] = [
            'class' => 'frontend\actions\AttributeController\CreateAction',
            'modelClass' => $this->modelClass,
        ];

        $actions['replace'] = [
            'class' => 'frontend\actions\AttributeController\ReplaceAction',
            'modelClass' => $this->modelClass,
        ];

        $actions['add'] = [
            'class' => 'frontend\actions\AttributeController\ReplaceAction',
            'modelClass' => $this->modelClass,
        ];

        $actions['index'] = [
            'class' => 'yii\rest\IndexAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],

            'prepareDataProvider' => function ($action, $filter) {
                $model = new $this->modelClass;
                $query = $model::find();
                if (!empty($filter)) {
                    $query->andWhere($filter);
                }

                $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => false,

                ]);
                return $dataProvider;
            },
            'dataFilter' => [
                'class' => 'yii\data\ActiveDataFilter',
                'searchModel' => 'common\models\AttributeSearch'
            ]
        ];

        return $actions;

    }
}
