<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\actions\FiledropAccountController;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;
use backend\components\MessagingService;

/**
 * Custom RestController action: update FiledropAccount model and send email
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class UpdateAction extends \yii\rest\UpdateAction
{
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;


    /**
     * Updates an existing model.
     * @param string $id the primary key of the model.
     * @return \yii\db\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $model->scenario = $this->scenario;
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        if ("1" === Yii::$app->getRequest()->getBodyParam('send') && Yii::$app->getRequest()->getBodyParam('to') && Yii::$app->getRequest()->getBodyParam('subject')) {
            $msgSrv = new MessagingService(Yii::$app->mailer);
            $msgSrv->sendEmailMessage("admin@gigadb.org",Yii::$app->getRequest()->getBodyParam('to'),Yii::$app->getRequest()->getBodyParam('subject'),$model->instructions);
        }
        return $model;
    }
}
