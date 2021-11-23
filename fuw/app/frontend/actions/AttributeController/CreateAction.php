<?php

namespace frontend\actions\AttributeController;

/**
 * CreateAction create attributes (single or multiple)
 *
 * For more details and usage information on UpdateAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */


use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

/**
 * CreateAction implements the API endpoint for creating a new model from the given data.
 *
 * For more details and usage information on CreateAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CreateAction extends \yii\rest\Action
{
    /**
     * @var string the scenario to be assigned to the new model before it is validated and saved.
     */
    public $scenario = Model::SCENARIO_DEFAULT;
    /**
     * @var string the name of the view action. This property is need to create the URL when the model is successfully created.
     */
    public $viewAction = 'view';


    /**
     * Creates a new model.
     * @return \yii\db\ActiveRecordInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     */
    public function run()
    {
        $modelClass = $this->modelClass ;
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        // collect POST data from body of the request
        $attr = Yii::$app->request->getBodyParams();
        // Yii::warning(var_export($attr,true));
        if( $attr && isset($attr["Attributes"]) ) {
            $attributes = [];
            $saved = [];
            // create an array of models
            $count = count($attr["Attributes"]);
            $attributes = [];
            for($i = 0; $i < $count; $i++) {
                $attributes[] = new $modelClass([
                                    'scenario' => $this->scenario,
                                ]);
                
            }

            // add new ones
            $loaded = $modelClass::loadMultiple($attributes, $attr, "Attributes");
            foreach($attributes as $loadedModel) {
                if ($loadedModel->save()) {
                    $saved[] = $loadedModel ;
                }
            }

            return $saved;
        }
        elseif ($attr && isset($attr["Attribute"]) ) {        
            /* @var $model \yii\db\ActiveRecord */
            $model = new $this->modelClass([
                'scenario' => $this->scenario,
            ]);

            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
            if ($model->save()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
                $id = implode(',', array_values($model->getPrimaryKey(true)));
                $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));
            } elseif (!$model->hasErrors()) {
                throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
            }

            return $model;
        }

    }
}
