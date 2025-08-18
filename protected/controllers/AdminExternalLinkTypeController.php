<?php

declare(strict_types=1);

class AdminExternalLinkTypeController extends Controller
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
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
            array('allow',
                  'actions'=>array('admin', 'create', 'view', 'update'),
                  'roles'=>array('admin'),
            ),
            array('deny',
                  'users'=>array('*'),
            ),
        );
    }

    public function actionAdmin()
    {
        $model = new ExternalLinkType();
        $model->scenario = 'search';
        $model->unsetAttributes();

        if ($attr = Yii::$app->request->get('ExternalLinkType', null)) {
            $model->setAttributes($attr);
        }

        $this->loadBaBbqPolyfills = true;

        return $this->render('admin',array(
            'model' => $model,
        ));
    }

    public function actionCreate()
    {
        $model = new ExternalLinkType();
        $model->scenario = 'create';

        if ($attr = Yii::$app->request->post('ExternalLinkType')) {
            $model->attributes = $attr;

            if ($model->save()) {
                $this->redirect(array('view','id'=>$model->id));
            }
        }

        return $this->render('create',array(
            'model'=>$model,
        ));
    }

    public function actionView($id)
    {
        return $this->render('view',array(
            'model'=>$this->loadModel($id),
        ));
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        if ($attr = Yii::$app->request->post('ExternalLinkType'))
        {
            $model->attributes = $attr;
            if ($model->save())
                $this->redirect(array('view','id' => $model->id));
        }

        return $this->render('update',array(
            'model' => $model,
        ));
    }

    private function loadModel($id)
    {
        $model = ExternalLinkType::model()->findByPk($id);
        if (!$model) {
            throw new CHttpException(404,'The requested page does not exist.');
        }

        return $model;
    }
}
