<?php
/**
 * Routing, aggregating and composing logic for Dataset submission
 */
class DatasetSubmissionController extends Controller
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',  // allow logged-in users to perform 'upload'
                'actions' => array(
                    'index',
                    'upload',
                    'study',
                    'author',
                    'additional',
                    'saveAdditional',
                    'funding',
                    'validateFunding',
                    'saveFundings',
                    'sample',
                    'saveSamples',
                    'checkUnit',
                    'end',
                ),
                'users' => array('@'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }


    /**
     * Index page.
     */
    public function actionIndex()
    {
        $this->render('index');
    }

    /**
     * Upload page.
     */
    public function actionUpload()
    {
        $file = CUploadedFile::getInstanceByName('xls');
        if ($file) {
            $loggedUser = MainHelper::getLoggedUser();
            if (MailHelper::sendUploadedDatasetToAdmin($loggedUser, $file->getTempName(), $file->getName())) {
                $this->redirect('/datasetSubmission/upload/status/successful');
            } else {
                $this->redirect('/datasetSubmission/upload/status/failed');
            }
        }

        $this->render('upload');
    }

    /**
     * Study page.
     */
    public function actionStudy()
    {
        if (isset($_GET['id'])) {
            $dataset = $this->getDataset($_GET['id']);
            $image = $dataset->image ?: new Images();
            $image->setIsNoImage($image->location == 'no_image.jpg');

            $this->isSubmitter($dataset);
        } else {
            $dataset = new Dataset();
            $image = new Images();
        }

        if (isset($_POST['Dataset']) && isset($_POST['Images'])) {
            $newKeywords = isset($_POST['keywords']) ? $_POST['keywords'] : '';
            $newTypes = isset($_POST['datasettypes']) ? $_POST['datasettypes'] : array();

            $image->loadByData($_POST['Images']);
            $dataset->loadByData($_POST['Dataset']);
            $dataset->types = $newTypes;
            $dataset->keywords = explode(',', $newKeywords);
            if ($dataset->validate() && $image->validate()) {
                $image->save();
                $dataset->image_id = $image->id;
                $dataset->save();

                $dataset->updateKeywords($newKeywords);
                $dataset->updateTypes($newTypes);

                $image->saveImageFile();

                if (isset($_POST['redirect_url']) && $_POST['redirect_url']) {
                    $this->redirect($_POST['redirect_url']);
                }
                $this->redirect(array('/datasetSubmission/study', 'id'=>$dataset->id));
            }
        }


        $this->render('study', array('model' => $dataset, 'image'=>$image));
    }

    /**
     * Author page.
     */
    public function actionAuthor()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        } else {
            $dataset = $this->getDataset($_GET['id']);

            $this->isSubmitter($dataset);

            $das = DatasetAuthor::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'rank asc'));
            $contributions = Contribution::model()->findAll(array('order'=>'name asc'));

            $this->render('author', array(
                'model' => $dataset,
                'das'=>$das,
                'contributions' => $contributions,
            ));
        }
    }

    /**
     * Additional page.
     */
    public function actionAdditional()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        } else {
            $dataset = $this->getDataset($_GET['id']);

            $this->isSubmitter($dataset);

            $links = Link::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

            $link_database = Yii::app()->db->createCommand()
                ->select("prefix")
                ->from("prefix")
                ->order("prefix asc")
                ->group("prefix")
                ->queryAll();

            $relations = Relation::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'related_doi asc'));

            $dps = DatasetProject::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

            $exLinks = ExternalLink::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

            $this->render('additional', array(
                'model' => $dataset,
                'links' => $links,
                'link_database' => $link_database,
                'relations' => $relations,
                'dps' => $dps,
                'exLinks' => $exLinks,
            ));
        }
    }

    /**
     * @throws CException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSaveAdditional() {
        if(isset($_POST['dataset_id'])) {
            $dataset = $this->getDataset($_POST['dataset_id']);

            $transaction = Yii::app()->db->beginTransaction();

            $links = Link::model()->findAllByAttributes(array('dataset_id'=>$dataset->id));
            $newLinks = isset($_POST['publicLinks']) && is_array($_POST['publicLinks']) ? $_POST['publicLinks'] : array();
            $needLinks = array();
            if ($newLinks) {
                foreach ($newLinks as $newLink) {
                    if (!$newLink['id']) {
                        $link = new Link;
                        $link->dataset_id = $_POST['dataset_id'];
                        $link->is_primary = true;
                        $link->link = $newLink['link'];

                        if (!$link->validate()) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Save Error."
                            ));
                        }

                        $link->save();
                    } else {
                        $needLinks[] = $newLink['id'];
                    }
                }

                $dataset->setAdditionalInformationKey(AIHelper::PUBLIC_LINKS, true);
                $dataset->save(false);
            } else {
                $dataset->setAdditionalInformationKey(AIHelper::PUBLIC_LINKS, false);
                $dataset->save(false);
            }


            foreach ($links as $link) {
                if (!in_array($link->id, $needLinks)) {
                    $link->delete();
                }
            }

            $relations = Relation::model()->findAllByAttributes(array('dataset_id'=>$dataset->id));
            $newRelations = isset($_POST['relatedDoi']) && is_array($_POST['relatedDoi']) ? $_POST['relatedDoi'] : array();
            $needRelations = array();
            if ($newRelations) {
                foreach ($newRelations as $newRelation) {
                    if (!$newRelation['id']) {
                        $relation = new Relation;
                        $relation->dataset_id = $_POST['dataset_id'];
                        $relation->related_doi = $newRelation['related_doi'];
                        $relation->relationship_id = $newRelation['relationship_id'];

                        if (!$relation->validate()) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Save Error."
                            ));
                        }

                        $relation->save();
                    } else {
                        $needRelations[] = $newRelation['id'];
                    }
                }

                $dataset->setAdditionalInformationKey(AIHelper::RELATED_DOI, true);
                $dataset->save(false);
            } else {
                $dataset->setAdditionalInformationKey(AIHelper::RELATED_DOI, false);
                $dataset->save(false);
            }

            foreach ($relations as $relation) {
                if (!in_array($relation->id, $needRelations)) {
                    $relation->delete();
                }
            }

            $projects = DatasetProject::model()->findAllByAttributes(array('dataset_id'=>$dataset->id));
            $newProjects = isset($_POST['projects']) && is_array($_POST['projects']) ? $_POST['projects'] : array();
            $needProjects = array();
            if ($newProjects) {
                foreach ($newProjects as $newProject) {
                    if (!$newProject['id']) {
                        $dp = new DatasetProject;
                        $dp->dataset_id = $_POST['dataset_id'];
                        $dp->project_id = $newProject['project_id'];

                        if (!$dp->validate()) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Save Error."
                            ));
                        }

                        $dp->save();
                    } else {
                        $needProjects[] = $newProject['id'];
                    }
                }

                $dataset->setAdditionalInformationKey(AIHelper::PROJECTS, true);
                $dataset->save(false);
            } else {
                $dataset->setAdditionalInformationKey(AIHelper::PROJECTS, false);
                $dataset->save(false);
            }

            foreach ($projects as $project) {
                if (!in_array($project->id, $needProjects)) {
                    $project->delete();
                }
            }

            $exLinks = ExternalLink::model()->findAllByAttributes(array('dataset_id'=>$dataset->id));
            $newExLinks = isset($_POST['exLinks']) && is_array($_POST['exLinks']) ? $_POST['exLinks'] : array();
            $needExLinks = array();
            if ($newExLinks) {
                $hasManuscripts = false;
                $hasProtocols = false;
                $has3d = false;
                $hasCodes = false;
                $hasSources = false;
                foreach ($newExLinks as $newExLink) {
                    if (!$newExLink['id']) {
                        $exLink = new ExternalLink;
                        $exLink->loadByData($newExLink);

                        if (!$exLink->validate()) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Save Error."
                            ));
                        }

                        $exLink->save();
                    } else {
                        $needExLinks[] = $newExLink['id'];
                    }

                    switch ($newExLink['externalLinkType']) {
                        case AIHelper::MANUSCRIPTS:
                            $hasManuscripts = true;
                            break;
                        case AIHelper::PROTOCOLS:
                            $hasProtocols = true;
                            break;
                        case AIHelper::_3D_IMAGES:
                            $has3d = true;
                            break;
                        case AIHelper::CODES:
                            $hasCodes = true;
                            break;
                        default:
                            $hasSources = true;
                            break;
                    }
                }

                $dataset->setAdditionalInformationKey(AIHelper::MANUSCRIPTS, $hasManuscripts);
                $dataset->setAdditionalInformationKey(AIHelper::PROTOCOLS, $hasProtocols);
                $dataset->setAdditionalInformationKey(AIHelper::_3D_IMAGES, $has3d);
                $dataset->setAdditionalInformationKey(AIHelper::CODES, $hasCodes);
                $dataset->setAdditionalInformationKey(AIHelper::SOURCES, $hasSources);
                $dataset->save(false);
            } else {
                $dataset->setAdditionalInformationKey(AIHelper::MANUSCRIPTS, false);
                $dataset->setAdditionalInformationKey(AIHelper::PROTOCOLS, false);
                $dataset->setAdditionalInformationKey(AIHelper::_3D_IMAGES, false);
                $dataset->setAdditionalInformationKey(AIHelper::CODES, false);
                $dataset->setAdditionalInformationKey(AIHelper::SOURCES, false);
                $dataset->save(false);
            }

            foreach ($exLinks as $exLink) {
                if (!in_array($exLink->id, $needExLinks)) {
                    $exLink->delete();
                }
            }

            $transaction->commit();
            Util::returnJSON(array("success"=>true));
        }

        Util::returnJSON(array("success"=>false,"message"=>"Data is empty."));
    }

    /**
     * Funding page.
     */
    public function actionFunding()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        } else {
            $dataset = $this->getDataset($_GET['id']);

            $this->isSubmitter($dataset);

            $funders = Funder::model()->findAllByAttributes(array(), array('order'=>'primary_name_display asc'));
            $fundings = Funding::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'id asc'));

            $this->render('funding', array(
                'model' => $dataset,
                'funders' => $funders,
                'fundings' => $fundings,
            ));
        }
    }

    public function actionValidateFunding() {
        if ($_POST) {
            $funding = new Funding();
            $funding->loadByData($_POST);

            if($funding->validate()) {
                Util::returnJSON( array(
                    "success" => true,
                    'funding' => $funding->asArray(),
                ));
            }
            Util::returnJSON(array(
                "success"=>false,
                "message"=>current($funding->getErrors())
            ));
        }

        Util::returnJSON(array(
            "success"=>false,
            "message"=> "Data is empty."
        ));
    }

    /**
     * @throws CException
     */
    public function actionSaveFundings() {
        if(isset($_POST['dataset_id'])) {
            $transaction = Yii::app()->db->beginTransaction();

            $dataset = $this->getDataset($_POST['dataset_id']);
            $hasFunding = 0;

            $fundings = $dataset->fundings;

            $newFundings = isset($_POST['fundings']) && is_array($_POST['fundings']) ? $_POST['fundings'] : array();
            $needFundings = array();
            if ($newFundings) {
                foreach ($newFundings as $newFunding) {
                    if (!$newFunding['id']) {
                        $funding = new Funding();
                        $funding->loadByData($newFunding);
                        if (!$funding->validate()) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Save Error."
                            ));
                        }

                        $funding->save();
                    } else {
                        $needFundings[] = $newFunding['id'];
                    }
                }

                $hasFunding = 1;
            }

            $dataset->funding = $hasFunding;
            if (!$dataset->save(false)) {
                $transaction->rollback();
                Util::returnJSON(array(
                    "success"=>false,
                    "message"=>"Save Error."
                ));
            }

            foreach ($fundings as $funding) {
                if (!in_array($funding->id, $needFundings)) {
                    if (!$funding->delete()) {
                        $transaction->rollback();
                        Util::returnJSON(array(
                            "success"=>false,
                            "message"=>"Save Error."
                        ));
                    }
                }
            }

            $transaction->commit();
            Util::returnJSON(array("success"=>true));
        }

        Util::returnJSON(array("success"=>false,"message"=>"Data is empty."));
    }

    /**
     * Sample page.
     */
    public function actionSample()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        }

        $dataset = $this->getDataset($_GET['id']);

        $this->isSubmitter($dataset);

        $error = '';
        $rows = array();
        if ($_POST) {
            $samples = CUploadedFile::getInstanceByName('samples');
            if($samples) {

                if ($samples->getType() != CsvHelper::TYPE_CSV && $samples->getType() != CsvHelper::TYPE_TSV) {
                    $error = "File has wrong extension.";
                } else {
                    $delimiter = $samples->getType() == CsvHelper::TYPE_CSV ? ';' : "\t";
                    $rows = CsvHelper::getArrayByFileName($samples->getTempName(), $delimiter);
                    if (!$rows) {
                        $error = "File is empty.";
                    } else {
                        $lastRequired = 2;
                        for ($j = 3, $k = count($rows[0]); $j < $k; $j++) {
                            if (!empty($rows[0][$j])) {
                                $lastRequired = $j;
                            }
                        }

                        for ($i = 1, $n = count($rows); $i < $n; $i++) {
                            for ($j = 0; $j <= $lastRequired; $j++)
                            if (empty($rows[$i][$j])) {
                                $error = 'Row ' . ($i + 1) . ': ' . 'Column ' . ($j + 1) . ' cannot be blank.';
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        $template = isset($_GET['template']) ? $this->getSampleTemplate($_GET['template']) : null;

        $units = Unit::model()->findAll(array('order'=>'name asc'));

        $sts = SampleTemplate::model()->findAll(array('order'=>'name asc'));

        $samples = $dataset->samples;

        $sampleIds = array();
        foreach ($samples as $sample) {
            $sampleIds[] = $sample->id;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition("sample_id", $sampleIds);
        $sas = SampleAttribute::model()->findAll($criteria, array('order'=>'attribute_id asc'));

        $uniques = array();
        foreach ($sas as $key => $sa) {
            $unique = $sa->attribute_id . '-' . $sa->unit_id;
            if (in_array($unique, $uniques)) {
                unset($sas[$key]);
            } else {
                $uniques[] = $unique;
            }
        }

        $species = Species::model()->findAll(array('order'=>'common_name asc'));
        $attrs = Attribute::model()->findAll(array('order'=>'attribute_name asc'));

        $this->render('sample', array(
            'model' => $dataset,
            'template' => $template,
            'units' => $units,
            'samples' => $samples,
            'sas' => $sas,
            'sts' => $sts,
            'error' => $error,
            'rows' => $rows,
            'species' => $species,
            'attrs' => $attrs,
        ));
    }

    /**
     * @throws CException
     */
    public function actionSaveSamples() {
        if(isset($_POST['dataset_id'])) {
            $transaction = Yii::app()->db->beginTransaction();

            $dataset = $this->getDataset($_POST['dataset_id']);

            $attrs = array();
            $newSampleAttrs = isset($_POST['sample_attrs']) && is_array($_POST['sample_attrs']) ? $_POST['sample_attrs'] : array();
            foreach ($newSampleAttrs as $i => $newSampleAttr) {
                $attr = Attribute::model()->findByAttributes(array('attribute_name' => $newSampleAttr['attr_name']));
                if (!$attr) {
                    $attr = new Attribute;
                    $attr->attribute_name = $newSampleAttr['attr_name'];
                    if (!$attr->validate()) {
                        $transaction->rollback();
                        $error = current($attr->getErrors());
                        Util::returnJSON(array(
                            "success"=>false,
                            "message"=> 'Col ' . ($i + 4) . ': ' . current($error)
                        ));
                    }
                    $attr->save();
                }

                $attrs[] = $attr;
            }

            /** @var Sample[] $samples */
            $samples = $dataset->samples;
            $newSamples = isset($_POST['samples']) && is_array($_POST['samples']) ? $_POST['samples'] : array();
            $needSamples = array();
            if ($newSamples) {
                foreach ($newSamples as $key => $newSample) {
                    if (!$newSample['id']) {
                        $sample = new Sample();
                        $ds = new DatasetSample;
                        $ds->dataset_id = $dataset->id;
                    } else {
                        $sample = Sample::model()->findByPk($newSample['id']);
                        $ds = DatasetSample::model()->findByAttributes(array('sample_id' => $newSample['id'], 'dataset_id' => $dataset->id));
                        if (!$sample || !$ds) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=>"Save Error."
                            ));
                        }

                        $needSamples[] = $newSample['id'];
                    }

                    $sample->loadByData($newSample);
                    if (!$sample->validate()) {
                        $transaction->rollback();
                        $error = current($sample->getErrors());
                        Util::returnJSON(array(
                            "success"=>false,
                            "message"=> 'Row ' . ($key + 1) . ': ' . current($error)
                        ));
                    }

                    $sample->save();
                    $ds->sample_id = $sample->id;
                    $ds->save();

                    $needAttrs = array();
                    foreach ($attrs as $i => $attr) {
                        if (!$newSample['attr_values'][$i]) {
                            $transaction->rollback();
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=> 'Row ' . ($key + 1) . ': ' . 'Value for ' . $attr->attribute_name . ' cannot be blank.',
                            ));
                        }

                        $unitId = isset($_POST['sample_attrs'][$i]['unit_id']) && $_POST['sample_attrs'][$i]['unit_id']
                            ? $_POST['sample_attrs'][$i]['unit_id'] : null;
                        $sa = SampleAttribute::model()->findByAttributes(array(
                            'sample_id' => $sample->id,
                            'attribute_id' => $attr->id,
                            'unit_id' => $unitId
                        ));
                        if (!$sa) {
                            $sa = new SampleAttribute();
                            $sa->sample_id = $sample->id;
                            $sa->attribute_id = $attr->id;
                            $sa->unit_id = $unitId;
                        }

                        $sa->value = $newSample['attr_values'][$i];

                        if (!$sa->validate()) {
                            $transaction->rollback();
                            $error = current($sa->getErrors());
                            Util::returnJSON(array(
                                "success"=>false,
                                "message"=> 'Row ' . ($key + 1) . ', Col ' . ($i + 4) . ': ' . current($error)
                            ));
                        }

                        $sa->save();
                        $needAttrs[] = $attr->id;
                    }

                    $sas = SampleAttribute::model()->findAllByAttributes(array('sample_id' => $sample->id));
                    foreach ($sas as $sa) {
                        if (!in_array($sa->attribute_id, $needAttrs)) {
                            if (!$sa->delete()) {
                                $transaction->rollback();
                                Util::returnJSON(array(
                                    "success"=>false,
                                    "message"=>"Save Error."
                                ));
                            }
                        }
                    }
                }
            }

            foreach ($samples as $sample) {
                if (!in_array($sample->id, $needSamples)) {
                    if (!$sample->delete()) {
                        $transaction->rollback();
                        Util::returnJSON(array(
                            "success"=>false,
                            "message"=>"Save Error."
                        ));
                    }
                }
            }

            $transaction->commit();
            Util::returnJSON(array("success"=>true));
        }

        Util::returnJSON(array("success"=>false,"message"=>"Data is empty."));
    }

    public function actionCheckUnit() {
        if(isset($_GET['attr_name'])) {
            $attr = Attribute::model()->findByAttributes(array('attribute_name' => $_GET['attr_name']));

            if ($attr && $attr->allowed_units) {
                Util::returnJSON(array(
                    "success"=>true,
                    'unitId' => $attr->allowed_units
                ));
            }
        }

        Util::returnJSON(array("success"=>false));
    }

    /**
     * End page.
     */
    public function actionEnd()
    {
        if (!isset($_GET['id'])) {
            $this->redirect("/user/view_profile");
        } else {
            $dataset = $this->getDataset($_GET['id']);
            $dataset->upload_status = 'UserUploadingData';
            $dataset->save(false);

            $this->isSubmitter($dataset);

            $this->render('end', array('model' => $dataset));
        }
    }

    protected function getDataset($id)
    {
        $dataset = Dataset::model()->findByPk($id);

        if (!$dataset) {
            $this->redirect("/user/view_profile");
        }

        return $dataset;
    }

    protected function getSampleTemplate($id)
    {
        $template = SampleTemplate::model()->findByPk($id);

        return $template;
    }

    protected function isSubmitter(Dataset $dataset)
    {
        if ($dataset->submitter_id != Yii::app()->user->id) {
            Yii::app()->user->setFlash('keyword', "You are not the owner of dataset");
            $this->redirect("/user/view_profile");
        }

        return true;
    }
}
