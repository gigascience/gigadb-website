<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\actions\FiledropAccountController;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;
use backend\models\MoveJob;

/**
 * A custom RestController action to create and post a worker job for moving files to public ftp
 *
 * It's a specialisation to allow a dry-run mode
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class MoveFilesAction extends \yii\rest\Action
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
    public function run($id)
    {
        Yii::info("Move Files for $id");
        $filedrop = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $filedrop);
        }

        $jobs = [];
        $files = $filedrop->getUploads();
        foreach ($files as $file) {
            $jid = Yii::$app->queue->push(new MoveJob([
                'doi' => $filedrop->doi,
                'file' => $file,
            ]));       
            $jobs[] =  ["file" => $file, "jobId" => $jid];   
        }

        return [ "doi" => $filedrop->doi, "jobs" => $jobs ];
    }
}
