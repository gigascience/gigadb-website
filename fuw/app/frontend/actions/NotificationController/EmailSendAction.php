<?php

namespace frontend\actions\NotificationController;

/**
 * UpdateAction implements the API endpoint for updating a model.
 *
 * For more details and usage information on UpdateAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */

use Yii;
use yii\web\ServerErrorHttpException;
use common\components\MessagingService;

class EmailSendAction extends \yii\base\Action
{

    /**
     * Updates an existing model.
     * @return array  status of email sending
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run()
    {
        $data = Yii::$app->getRequest()->getBodyParams();
        $msgSrv = new MessagingService(Yii::$app->mailer);
        $status = $msgSrv->sendEmailMessage(
            $data['sender'],
            $data['recipient'],
            $data['subject'],
            $data['content']
        );


        return ["status" => $status ? "sent" : "error", 'type' => 'email', 'environment' =>'development'];
    }
}
