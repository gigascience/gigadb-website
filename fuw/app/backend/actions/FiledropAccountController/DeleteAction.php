<?php

namespace backend\actions\FiledropAccountController;

use Yii;
use yii\web\ServerErrorHttpException;
use backend\models\FiledropAccount;

/**
 * A custom RestController action for deleting a FiledropAccount model
 *
 * Instead of deleting the model, it marks the model as FiledropAccount::STATUS_TERMINATED
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class DeleteAction extends \yii\rest\DeleteAction
{
	/**
     * Deletes a Filedrop Account from UX by setting status as 'terminated'
     * @param mixed $id id of the model to be deleted.
     * @throws ServerErrorHttpException on failure.
     */
    public function run($id)
    {
        //$account = FiledropAccount::findOne(["doi" => $id]);
        $account = $this->findModel($id);

        if (!isset($account)) {
            throw new ServerErrorHttpException('Account to delete cannot be found');
        }

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $account->id, $account);
        }

        $account->status = FiledropAccount::STATUS_TERMINATED;

        if ( ! $account->validate() ) {
        	throw new ServerErrorHttpException('validation failed:'.implode("\n", $account->getErrorSummary(true)));
        }
        else if ( ! $account->save(false) ) {
            throw new ServerErrorHttpException("Failed to terminate account for dataset ".$account->doi);
        }
        else {
        	Yii::info("Terminated Filedrop Account for dataset ".$account->doi);
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}