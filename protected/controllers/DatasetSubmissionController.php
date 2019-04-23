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

            $this->render('author', array('model' => $dataset,'das'=>$das));
        }
    }

    /**
     * Author page.
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

    protected function getDataset($id)
    {
        $dataset = Dataset::model()->findByPk($id);

        if (!$dataset) {
            $this->redirect("/user/view_profile");
        }

        return $dataset;
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
