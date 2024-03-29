<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\actions\UserController;

use Yii;

/**
 * ViewAction implements the API endpoint for returning the detailed information about a model.
 *
 * For more details and usage information on ViewAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LookupAction extends \yii\rest\Action
{
    /**
     * Displays a model.
     * @param string $id the primary key of the model.
     * @return \yii\db\ActiveRecordInterface the model being displayed
     */
    public function run()
    {
        $modelClass = $this->modelClass ;
        $email = Yii::$app->getRequest()->getBodyParam("email");
        $model = $modelClass::findOne(["email" => $email]);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }
        return $model;
    }
}
