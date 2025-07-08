<?php

declare(strict_types=1);

class PolicyController extends CController
{
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
            array('allow', // admin only
                'actions' => array('create'),
                'roles' => array('admin'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionCreate()
    {
        $model = new DatasetAttributes();
        $att = Attributes::model()->findByAttributes(array('attribute_name' => Attributes::FUP));
        if (!$att) {
            $att = new Attributes();
            $att->attribute_name = Attributes::FUP;
            $att->definition = '';
            $att->save(); // TODO: what should we do if it fails
        }
        $model->attribute_id = $att->id;
        $image = new Images();

        if ($args = Yii::$app->request->post('DatasetAttributes')) {
            $exist = DatasetAttributes::model()->findByAttributes(array('dataset_id' => $args['dataset_id'], 'attribute_id' => $att->id));
            if ($exist) {
                $model = $exist;
            }
            $model->attributes = $args;
            $model->value = '';

            $image->license = "no license";
            $image->photographer = "no author";
            $image->source = "gigadb";
            if ($image->validate() && $image->save()) {
                $model->image_id = $image->id;
            } else {
                Yii::log(print_r($image->getErrors(), true), 'debug');
            }

            if ($model->validate() && $model->save()) {
                $this->redirect('/dataset/' . $model->dataset->identifier);
            }
            Yii::log(print_r($model->getErrors(), true), 'debug');
        }

        $this->render('create', array('model' => $model, 'image' => $image));
    }
}
