<?php

namespace frontend\actions\AttributeController;

/**
 * ReplaceAction delete and create attributes
 *
 * For more details and usage information on UpdateAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */

use Yii;
use yii\web\ServerErrorHttpException;

class ReplaceAction extends \yii\base\Action
{

    /**
     * delete/create multiple attributes for a given upload_id
     * @param $upload_id id of the upload object the attributes are associated with
     * @return array new stored attributes
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run($upload_id)
    {
        return "{}";
    }
}
