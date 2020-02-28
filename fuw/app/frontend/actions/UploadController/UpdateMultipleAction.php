<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\actions\UploadController;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;
use common\models\Upload;

/**
 * UpdateAction implements the API endpoint for updating a model.
 *
 * For more details and usage information on UpdateAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UpdateMultipleAction extends \yii\rest\UpdateAction
{
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;


    /**
     * Updates an existing model.
     * @param string $doi DOI for the uploads we want to update
     * @return \yii\db\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run($doi)
    {
        $uploads = Upload::find()->where(["doi" => $doi])->indexBy('id')->all();
        if (Model::loadMultiple($uploads, Yii::$app->request->post(), "Uploads") 
        && Model::validateMultiple($uploads)) {
            foreach ($uploads as $upload) {
                $upload->save(false);
            }
            return $uploads;
        }
        throw new ServerErrorHttpException('Failed to update the uploads');

    }
}
