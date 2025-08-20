<?php

declare(strict_types=1);

class AdminDatasetSampleController extends Controller
{
    /**
     * @return string[] action filters
     */
    public function filters(): array
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array<int, array<int|string, list<string>|string>> access control rules
     */
    public function accessRules(): array
    {
        return array(
            array('allow', // admin only
                'actions' => array('admin','delete','index','view','create','update'),
                'roles' => array('admin'),
            ),
                         array('allow',
                'actions' => array('create1', 'delete1', 'autocomplete','addSample','deleteSample','addSampleAttr','deleteSampleAttr','attributesList','updateSampleAttribute'),
                'users' => array('@')),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param int $id the ID of the model to be displayed
     */
    public function actionView(int $id): void
    {
        $model = $this->loadModel($id);

        $this->render('view', array('model' => $model));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate(): void
    {
        $model = new DatasetSample();

        if ($datasetSample = Yii::$app->request->post('DatasetSample')) {
            $model->attributes = $datasetSample;
            if ($model->save()) {
                $this->redirect(array('view','id' => $model->id));
            }
        }

        $this->render('create', array('model' => $model));
    }

    public function actionAutocomplete(): void
    {
        /** @var CWebApplication $app */
        $app = Yii::app();

        if ($partial_sample_term = Yii::$app->request->get('term')) {
            $autoCompleteService = $app->autocomplete;
            $result = $autoCompleteService->findSpeciesLike($partial_sample_term);

            echo CJSON::encode($result);
            Yii::app()->end();
        }
    }

    //TODO: not used atm
    public function actionDelete1($id)
    {
        if (isset($_SESSION['samples'])) {
            $info = $_SESSION['samples'];
            foreach ($info as $key => $value) {
                if ($value['id'] == $id) {
                    unset($info[$key]);
                    $_SESSION['samples'] = $info;
                    $condition = 'id=' . $id;

                    $sample_id = DatasetSample::model()->findByAttributes(array('id' => $id))->sample_id;
                    DatasetSample::model()->deleteAll($condition);
                    Sample::model()->deleteAll('id=' . $sample_id);

                    $this->redirect("/adminDatasetSample/create1");
                }
            }
        }
    }

    //TODO: not used atm
    private function storeSample(&$model, &$id)
    {


        if (isset($_SESSION['dataset_id'])) {
            $dataset_id = $_SESSION['dataset_id'];
            //1) find species id
            $species_id = 0;
            $tax_id = $model->tax_id;
            $name = $model->species;
            $model->sample_id=0;
            //validate
            if (!$model->validate()) {
                return false;
            }
            //-1 means it doesn't exit in our database
            if ($model->tax_id != -1) {

                $species = Species::model()->findByAttributes(array('tax_id' => $tax_id));
                 $species_id = $species->id;
            } else {
                $species = Species::model()->findByAttributes(array('common_name' => $name));
                if ($species != NULL) {
                    $species_id = $species->id;
                } else {
                    $species = Species::model()->findByAttributes(array('scientific_name' => $name));
                    if ($species != NULL)
                        $species_id = $species->id;
                    else {
                        //insert a new species record
                        $model->addError('comment', 'The species you input is not in our database, please
                            input 0:new organism and contact
                        <a href=&quot;mailto:database@gigasciencejournal.com&quot;>database@gigasciencejournal.com</a>.');
                        return false;
                    }
                }
            }
            //2) insert sample
            $sample = new Sample;
            $sample->species_id = $species_id;
            $sample->code = $model->code;

            if (!$sample->save()) {
                $model->addError('error', 'Sample save error');
                return false;
            }
            $sample_id = $sample->id;


            //3) insert dataset_sample

            $model->sample_id = $sample_id;
            $model->dataset_id = $dataset_id;

            if (!$model->save()) {
                $model->addError('keyword', 'Dataset_Sample is not stored!');
                return false;
            }

            $id = $model->id;
            return true;
        }

        return false;
        echo('xxxxxxx');
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * TODO: not used atm
     */
    public function actionCreate1()
    {

        $model = new DatasetSample;
        $model->dataset_id = 1;

        if (!isset($_SESSION['samples']))
            $_SESSION['samples'] = array();

        $samples = $_SESSION['samples'];

        if (isset($_POST['DatasetSample'])) {


            $model->attributes = $_POST['DatasetSample'];

            $name = $_POST['DatasetSample']['code'];
            $tax_id = -1;
            $species = 0;
            if (strpos($_POST['DatasetSample']['species'], ":") !== false) {
                $array = explode(":",$_POST['DatasetSample']['species']);
                $tax_id = $array[0];
                $species = $_POST['DatasetSample']['species'];
            } else {
                $species = $_POST['DatasetSample']['species'];
            }
            $attrs = $_POST['DatasetSample']['attribute'];

            $model->code = $name;
            $model->species = $species;
            $model->tax_id = $tax_id;
            $model->attribute = $attrs;

            $id = 0;

            if(strpos($name,'SAMPLE') == 0)
            {
                $attribute_temp=null;
                $species1=null;
                $tax_id1=-1;
                $temp=explode(':', $name);
                if($temp[0]=='SAMPLE')
                {
                    $xmlpath=  'http://www.ebi.ac.uk/ena/data/view/'."$temp[1]".'&display=xml';
                    $allfile= simplexml_load_file($xmlpath);

                   foreach ($allfile->SAMPLE->SAMPLE_ATTRIBUTES->SAMPLE_ATTRIBUTE as $child)
                {
                    if($child->TAG=='Sample type'||$child->TAG=='Time of sample collection'||$child->TAG=='Habitat'||$child->TAG=='Sample extracted from')
                        $attribute_temp.= $child->TAG." = \"".$child->VALUE."\",";
                }
                $attribute_temp.="Description = \"".$allfile->SAMPLE->DESCRIPTION."\",";
                    foreach($allfile->SAMPLE->SAMPLE_NAME as $child)
                    {
                        if($child->TAG=='TAXON_ID')
                        {
                            $species1.=$child->VALUE.":";
                            $tax_id1=$child->VALUE;
                        }
                        if($child->TAG=='SCIENTIFIC_NAME')
                            $species1.=$child->VALUE.",";
                        if($child->TAG=='COMMON_NAME')
                            $species1.=$child->VALUE;

                    }


                }
                     $attrs=$attribute_temp;;
                     $model->attribute = $attrs;
            }


            if ($this->storeSample($model, $id)) {

                $newItem = array('id' => $id, 'name' => $name, 'species' => $species, 'attrs' => $attrs);

                array_push($samples, $newItem);
                $_SESSION['samples'] = $samples;
                $model = new DatasetSample;
            }
            else{
                $model->species="";
            }
        }

        $sample_model = new CArrayDataProvider($samples);

        $this->render('create1', array(
            'model' => $model,
            'sample_model' => $sample_model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id the ID of the model to be updated
     */
    public function actionUpdate(int $id): void
    {
        $model = $this->loadModel($id);

        if ($datasetSample = Yii::$app->request->post('DatasetSample')) {
            $model->attributes = $datasetSample;
            if ($model->save()) {
                $this->redirect(array('view','id' => $model->id));
            }
        }

        $this->render('update', array('model' => $model));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param int $id the ID of the model to be deleted
     */
    public function actionDelete(int $id): void
    {
        if (!Yii::app()->request->isPostRequest) {
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
        }

        // we only allow deletion via POST request
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!Yii::$app->request->get('ajax')) {
            $returnUrl = Yii::$app->request->post('returnUrl');

            $this->redirect($returnUrl ?: array('admin'));
        }
    }

    /**
     * Lists all models.
     */
    public function actionIndex(): void
    {
        $dataProvider = new CActiveDataProvider('DatasetSample');

        $this->render('index', array('dataProvider' => $dataProvider));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin(): void
    {
        $model = new DatasetSample('search');
        $model->unsetAttributes();  // clear any default values

        if ($datasetSample = Yii::$app->request->get('DatasetSample')) {
            $model->setAttributes($datasetSample);
        }

        $this->loadBaBbqPolyfills = true;

        $this->render('admin', array('model' => $model));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param int $id the ID of the model to be loaded
     */
    public function loadModel(int $id): DatasetSample
    {
        $model = DatasetSample::model()->findByPk($id);

        if (!$model) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    /**
     * Performs the AJAX validation.
     *
     * @param CModel $model the model to be validated
     */
    protected function performAjaxValidation(CModel $model): void
    {
        if (Yii::$app->request->post('ajax') === 'dataset-sample-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionAddSample(): void
    {
        /** @var CWebApplication $app */
        $app = Yii::app();

        $datasetId = Yii::$app->request->post('dataset_id');
        $sampleName = Yii::$app->request->post('sample_name');
        $species = Yii::$app->request->post('species');

        if (!$datasetId || !$sampleName || !$species) {
            Util::returnJSON(array('success' => false, 'message' => Yii::t('app', 'Cannot add sample,Please select some values')));
        }

        $transaction = Yii::app()->db->beginTransaction();
        try {
            $array = explode(":", $species);
            $tax_id = $array[0];
            $species = Species::model()->findByAttributes(array('tax_id' => $tax_id));

            if (!$species) {
                Util::returnJSON(array("success" => false,"message" => Yii::t("app", 'Cannot add sample, please input "valid  species" value.')));
            }

            #create new sample
            $sample = new Sample();
            $sample->species_id = $species->id;
            $sample->name = $sampleName;
            $sample->submitted_id = $app->user->id;
            $sample->submission_date = date('Y-m-d H:i:s');

            $user = User::model()->findByPk($app->user->id);
            if ($user) {
                $sample->contact_author_name  = $user->first_name . " " . $user->last_name;
                $sample->contact_author_email = $user->email;
            }


            if (!$sample->save()) {
                Util::returnJSON(array('success' => false, 'message' => Yii::t('app', 'Save Error.')));
            }

            #create dataset sample
            $ds = new DatasetSample();
            $ds->dataset_id = $datasetId;
            $ds->sample_id = $sample->id;
            if (!$ds->save()) {
                $transaction->rollBack();
                Util::returnJSON(array('success' => false, 'message' => Yii::t('app', 'Save Error.')));
            }

            $transaction->commit();
            Util::returnJSON(array("success" => true));
        } catch (Exception $e) {
            $message = $e->getMessage();
            Yii::log(print_r($message, true), 'error');
            $transaction->rollback();

            Util::returnJSON(array("success" => false, "message" => Yii::t("app", 'Cannot add sample, please input "valid  species" value.')));
        }
    }

    public function actionDeleteSample(): void
    {
        if (!$dsId = Yii::$app->request->post('ds_id')) {
            Util::returnJSON(array('success' => false, 'message' => Yii::t('app', 'An error occured. Please try again.')));
        }

        try {
            $ds = DatasetSample::model()->findByPk($dsId);
            if ($ds->delete()) {
                Util::returnJSON(array("success" => true));
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            Yii::log(print_r($message, true), 'error');

            Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Delete Error.")));
        }

        Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Delete Error.")));
    }

    public function actionAddSampleAttr(): void
    {
        $sampleId = Yii::$app->request->post('sample_id');
        $attrId = Yii::$app->request->post('attr_id');
        $attrValue = Yii::$app->request->post('attr_value');
        $attrUnit = Yii::$app->request->post('attr_unit');

        if (!$sampleId || !$attrId || !$attrValue || !$attrUnit) {
            Util::returnJSON(array('success' => false, 'message' => Yii::t('app', 'Cannot add sample attr.')));
        }

        if (strlen($attrId) < 3) {
            Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Please enter an Attribute name with more than 3 characters.")));
        }

        $lastSa = SampleAttribute::model()->find(array('order' => 'id desc'));

        $sa = new SampleAttribute();
        if ($lastSa) {
            $sa->id = $lastSa->id + 1;
        }

        // try to find attribute, if not found, create a new one
        $attr = Attributes::model()->findByAttributes(array('attribute_name' => $attrId));
        if (!$attr) {
            #create new attribute
            $attr = new Attributes();
            $attr->attribute_name = $attrId;
            $attr->save(false); //TODO: why??
        }

        $sa->sample_id = $sampleId;
        $sa->attribute_id = $attr->id;
        $sa->value = $attrValue;

        if ($attrUnit) {
            $sa->unit_id = $attrUnit;
        }

        if ($sa->save()) {
            Util::returnJSON(array("success" => true));
        }

        Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Cannot add sample attr.")));
    }

    public function actionDeleteSampleAttr(): void
    {
        if (!$saId = Yii::$app->request->post('sa_id')) {
            Util::returnJSON(array('success' => false, 'message' => Yii::t('app', 'Delete Error.')));
        }

        $sa = SampleAttribute::model()->findByPk($saId);
        if ($sa->delete()) {
            Util::returnJSON(array("success" => true));
        }

        Util::returnJSON(array("success" => false,"message" => Yii::t("app", "Delete Error.")));
    }

    public function actionUpdateSampleAttribute(): void
    {
        $saId = Yii::$app->request->post('sa_id');
        $saValue = Yii::$app->request->post('sa_value');

        if (!$saId || !$saValue) {
            Util::returnJSON(array('success' => false, 'message' => Yii::t('app', 'Update Error.')));
        }


        $sa = SampleAttribute::model()->findByPk($saId);
        if (!$sa) {
            Util::returnJSON(array("success" => false, "message" => Yii::t("app", "Cannot find the sample attribute.")));
        }

        $sa->value = $saValue;
        if ($sa->save()) {
            Util::returnJSON(array("success" => true));
        }

        Util::returnJSON(array("success" => false,"message" => Yii::t("app", "Update Error.")));
    }

    public function actionAttributesList(): void
    {
        $attrs = array();
        $result = array();

        if ($term = Yii::$app->request->get('term')) {
            $criteria = new CDbCriteria();
            $criteria->addSearchCondition('attribute_name', $term);
            $attrs = Attributes::model()->findAll($criteria);

            foreach ($attrs as $attr) {
                $result[$attr->attribute_name] = $attr->attribute_name;
            }

            echo CJSON::encode($result);
            Yii::app()->end();
        }
    }
}
