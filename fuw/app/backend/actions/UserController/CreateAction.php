<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\actions\UserController;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;
use common\models\User;

/**
 * A custom RestController action for creating a User model
 *
 * It's a specialisation to allow TODO
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class CreateAction extends \yii\rest\CreateAction
{
    /**
     * @var string the scenario to be assigned to the new model before it is validated and saved.
     */
    public $scenario = Model::SCENARIO_DEFAULT;
    /**
     * @var string the name of the view action. This property is needed to create the URL when the model is successfully created.
     */
    public $viewAction = 'view';


    /**
     * Creates a new model.
     * @return \yii\db\ActiveRecordInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     */
    public function run()
    {

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass([
            'scenario' => 'admin-created',
        ]);

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->setPassword(Yii::$app->security->generateRandomString());
        $model->generateAuthKey();
        $model->status = User::STATUS_ACTIVE;

        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the User object for unknown reason.');
        }

        return $model;
    }
}
