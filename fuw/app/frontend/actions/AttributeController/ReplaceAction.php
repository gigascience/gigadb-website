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
use yii\base\Model;
use common\models\Attribute;

class ReplaceAction extends \yii\rest\Action
{

    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;

    /**
     * delete/create multiple attributes for a given upload_id
     * @param $upload_id id of the upload object the attributes are associated with
     * @return array new stored attributes
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run($upload_id)
    {

        $modelClass = $this->modelClass ;
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        $attributes = [];
        $saved = [];

        // collect POST data from body of the request
        $attr = Yii::$app->request->getBodyParams();
        // Yii::warning(var_export($attr,true));
        if( $attr && isset($attr["Attributes"]) ) {

            if("attribute/replace" === Yii::$app->requestedRoute) {
                // delete existing ones
                Yii::info("Action mode: attribute/replace");
                foreach ($modelClass::findAll([ 'upload_id' => $upload_id ]) as $oldModel) {
                    $oldModel->delete();
                }
            }
            else {
                throw new ServerErrorHttpException('Unrecognized action:'.Yii::$app->requestedRoute);
            }

            // create an array of models
            $count = count($attr["Attributes"]);
            $attributes = [];
            for($i = 0; $i < $count; $i++) {
                $attributes[] = new $modelClass([
                                    'scenario' => $this->scenario,
                                ]);
                $attributes[$i]->upload_id = $upload_id;
                
            }

            // add new ones
            $loaded = $modelClass::loadMultiple($attributes, $attr, "Attributes");
            foreach($attributes as $loadedModel) {
                if ($loadedModel->save()) {
                    $saved[] = $loadedModel ;
                }
            }
        }

        // returned saved models
        return $saved;
    }
}
