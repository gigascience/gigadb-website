<?php

declare(strict_types=1);

use Ramsey\Uuid\Uuid;

use yii\swiftmailer\Mailer;
use yii\swiftmailer\Message;

class SiteController extends Controller {
    /**
 	 * Declares class-based actions.
	 */
	public function actions() {
		return array(
			'page'=>array(
				'class'=>'CViewAction',
			),
            'flysystem-status' =>array(
                'class' => 'application.controllers.Site.FlysystemAction'
            ),
		);
	}

	public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

	public function accessRules() {
        return array(
            array('allow',  // allow all users
                'actions'=>array('index','error','contact','mapbrowse','team','about','advisory','faq','term','help','privacy', 'login', 'loginAffiliate', 'logout', 'revoke', 'feed', 'Guide', 'Guidegenomic', 'Guideimaging', 'Guidemetabolomic', 'Guideepigenomic', 'Guidemetagenomic', 'Guidesoftware'),
                'users'=>array('*'),
                'ips'=>array('*'),
            ),
            array('allow',
                'actions' => array('flysystem-status'),
                'users'=>array('*'),
                'ips' => array("172.16.238.*"),
            ),
            array('allow', # admins
                'actions'=>array('admin', 'su'),
                'roles'=>array('admin'),
                'ips'=>array('*'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
            array('deny',  // deny all ips
                'ips'=>array('*'),
            ),
        );
    }

    /**
    *
    * Administration action
	*
	**/

	public function actionAdmin()
    {
		$this->render('admin');
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex() {
		$form = new SearchForm;  // Use for Form
		$dataset = new Dataset; // Use for auto suggestion

		$datasetModel = $this->getDatasetByType(0);  // Use for image slider content

        $publicIdsCount = Yii::app()->db->createCommand()
            ->select('COUNT(id)')
            ->from('dataset')
            ->where("upload_status = 'Published'")
            ->queryScalar();

		$datasettypes_hints = Type::model()->findAll(array('order' => 'name ASC'));

        $news = Yii::app()->newsAndFeedsService->getTodaysNews();
        $rss_arr = Yii::app()->newsAndFeedsService->getFeedsData();
        $feed_datasets = Yii::app()->newsAndFeedsService->getFeedDatasets(12);

        $db = Yii::app()->db;
        //Get dataset types number
        $sql_1="select * from homepage_dataset_type";
        $command = $db->createCommand($sql_1);
        $results = $command->queryAll();

        $sql_2="select * from sample_number";
        $command = $db->createCommand($sql_2);
        $count_sample = $command->queryScalar();

        $sql_3="select * from file_number";
        $command = $db->createCommand($sql_3);
        $count_file = $command->queryScalar();

        $command = Yii::app()->db->createCommand()
            ->select('SUM(f.size)')
            ->from('file f')
            ->join('dataset d', 'd.id = f.dataset_id')
            ->where('d.upload_status = :status', [':status' => 'Published']);

        $bytes = $command->queryScalar();
        $bytesFormatted = UnitHelper::specifySizeUnits((int) $bytes);

        foreach($results as $result) {
            switch ($result['name']) {
                case "Genome-Mapping":
                     $number_genome_mapping=$result['count'];
                     break;
                case "Ecology":
                     $number_ecology=$result['count'];
                     break;
                case "ElectroEncephaloGraphy(EEG)":
                     $number_eeg=$result['count'];
                     break;
                case "Epigenomic":
                     $number_epi=$result['count'];
                     break;
                case "Genomic":
                     $number_genomic=$result['count'];
                     break;
                case "Imaging":
                     $number_imaging=$result['count'];
                     break;
                case "Lipidomic":
                     $number_lipi=$result['count'];
                     break;
                case "Metabarcoding":
                     $number_metabarcoding=$result['count'];
                     break;
                case "Metagenomic":
                     $number_metagenomic=$result['count'];
                     break;
                case "Metadata":
                     $number_metadata=$result['count'];
                     break;
                case "Metabolomic":
                     $number_metabolomic=$result['count'];
                     break;
                case "Climate":
                     $number_climate=$result['count'];
                     break;
                case "Network-Analysis":
                     $number_na=$result['count'];
                     break;
                case "Neuroscience":
                     $number_ns=$result['count'];
                     break;
                case "Phenotyping":
                     $number_pt=$result['count'];
                     break;
                case "Proteomic":
                     $number_proteomic=$result['count'];
                     break;
                case "Software":
                     $number_software=$result['count'];
                     break;
                case "Transcriptomic":
                     $number_ts=$result['count'];
                     break;
                case "Virtual-Machine":
                     $number_vm=$result['count'];
                     break;
                case "Workflow":
                     $number_wf=$result['count'];
                     break;
            }
        }
		$this->render('index',array(
			'datasets'=>$datasetModel,
            'size'=>$bytesFormatted,
			'form'=>$form,
			'dataset'=>$dataset,
			'news'=>$news,
			'dataset_hint'=>$datasettypes_hints ,
			'rss_arr' => $rss_arr ,
			'count' => $publicIdsCount,
            'count_sample' => $count_sample,
            'count_file' => $count_file,
            'number_genome_mapping'=>$number_genome_mapping ?? 0,
            'number_climate' => $number_climate ?? 0,
            'number_ecology'=>$number_ecology ?? 0,
            'number_eeg'=>$number_eeg ?? 0,
            'number_epi'=>$number_epi ?? 0,
            'number_genomic'=>$number_genomic ?? 0,
            'number_imaging'=>$number_imaging ?? 0,
            'number_lipi'=>$number_lipi ?? 0,
            'number_metabarcoding'=>$number_metabarcoding ?? 0,
            'number_metabolomic'=>$number_metabolomic ?? 0,
            'number_metadata'=>$number_metadata ?? 0,
            'number_metagenomic'=>$number_metagenomic ?? 0,
            'number_na'=>$number_na ?? 0,
            'number_ns'=>$number_ns ?? 0,
            'number_pt'=>$number_pt ?? 0,
            'number_proteomic'=>$number_proteomic ?? 0,
            'number_software'=>$number_software ?? 0,
            'number_ts'=>$number_ts ?? 0,
            'number_vm'=>$number_vm ?? 0,
            'number_wf'=>$number_wf ?? 0,
            'feed_datasets'=>$feed_datasets
        )
		);
	}


	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError() {
	    if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) {
	    		echo $error['message'];
            }
	    	else {
	        	$this->render('error', $error);
            }
	    }
	}
    /**
     * These are the actions to handle Guideline page
     */

    public function actionGuide()
    {
        $this->render('guide');
    }

    public function actionGuidegenomic()
    {
        $this->render('guidegenomic');
    }

    public function actionGuideimaging()
    {
        $this->render('guideimaging');
    }

    public function actionGuidemetabolomic()
    {
        $this->render('guidemetabolomic');
    }

    public function actionGuideepigenomic()
    {
        $this->render('guideepigenomic');
    }

    public function actionGuidemetagenomic()
    {
        $this->render('guidemetagenomic');
    }

    public function actionGuidesoftware()
    {
        $this->render('guidesoftware');
    }

    /**
     * Display /site/contact page
     */
    public function actionContact()
    {
        $model = new ContactForm;
        if ($contactForm = Yii::$app->request->post('ContactForm')) {
            $model->attributes = $contactForm;
            if ($model->validate()) {
                try {
                    Yii::app()->mailService->sendEmail(Yii::app()->params['adminEmail'], Yii::app()->params['adminEmail'], Yii::app()->params['email_prefix'] . $model->subject, "Message from: " . $model->name . " <" . $model->email . ">\n\n" . $model->body);
                } catch (Swift_TransportException $ste) {
                    Yii::log("Problem sending email from contact page - " . $ste->getMessage(), "error");
                }
                Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }

        $this->render('contact', array('model' => $model));
    }
	/**
	*This method returns all dataset locations
	*/
	public function actionMapbrowse()
    {
	    $locations = Yii::app()->db->createCommand("SELECT d.identifier,  d.title, satt.value, sp.scientific_name as sciname, s.id as sampleid FROM dataset as d
					      INNER JOIN dataset_sample as dsam on dsam.dataset_id = d.id
						  INNER JOIN sample as s on s.id = dsam.sample_id
					      INNER JOIN sample_attribute as satt on satt.sample_id=s.id
						  INNER JOIN species as sp on sp.id = s.species_id
						  where satt.attribute_id = 269 and d.upload_status='Published' order by sampleid")->queryAll();

	    foreach ($locations as $location) {
            $locationValue = $location["value"];
            $locationValue = preg_replace('/\s+/', '', $locationValue);
            $formatCheck = preg_match('/-?[0-9]*[.][0-9]*[,]-?[0-9]*[.][0-9]*/',$locationValue);

            if (!$formatCheck === 1){
              continue;
            }

            $val = explode(',', $locationValue);
            if (strpos($val[0],'.') === false || !is_numeric($val[0])){
                continue;
            }
            if (strpos($val[1],'.') === false || !is_numeric($val[1])){
                continue;
            }
            $location["sciname"] = str_replace(",","",$location["sciname"]);
	    }

	    $this->render('mapbrowse', array('locations' => $locations));
	}

    public function actionTeam()
    {
		$this->render('team');
	}


	public function actionAbout()
    {
	    // Dont' remove this block, it is used for automated testing application logging and debug settings
        if(defined('YII_DEBUG') && YII_DEBUG === true) {
            $uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, Yii::app()->getRequest()->getUrl());
            Yii::log("******* URL SHA_1 UUIDv5: {$uuid->toString()} *******","warning");
        }
		$this->render('about');
	}

    public function actionAdvisory()
    {
		$this->render('advisory');
	}

	public function actionFaq()
    {
		$this->render('faq');
	}

	public function actionTerm()
    {
		$this->render('term');
	}

	public function actionHelp()
    {
		$this->render('help');
	}

	public function actionPrivacy()
    {
		$this->render('privacy');
	}

    public function getDatasetByType($type)
    {

 	if ($type > 0) {
        $models = Dataset::model()->findAllBySql("SELECT * FROM dataset JOIN dataset_type ON dataset.id=dataset_type.dataset_id WHERE dataset_type.type_id=:type_id AND dataset.upload_status = 'Published' order by publication_date desc limit 9", array(':type_id' => $type));
        } else {
            $models = Dataset::model()->findAllBySql("SELECT * FROM dataset WHERE dataset.upload_status = 'Published'  order by publication_date desc limit 9");
        }

        return $models;  }

	public function actionAjaxLoadDataset(){
		 $type=6;

		 if(isset($_POST['type'])) $type=$_POST['type'];

		 $datasetModel=$this->getDatasetByType($type);
		 $this->renderPartial('slider',array('datasets'=>$datasetModel));

	}
	/**
	 * Displays the login page
	 */
	public function actionLogin() {

        $model = new LoginForm;
        if (isset($_GET['redirect']) && isset($_GET['username']) && isset($_GET['password'])) {
            $model->username = $_GET['username'];
            $model->password = $_GET['password'];
            $model->rememberMe = FALSE;
            if ($model->validate()) {
                $this->redirect('/user/changepassword');
            } else {

                $this->render('login', array('model' => $model));
            }
        }
        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            $model->username = strtolower($_POST['LoginForm']['username']);
            // validate user input and redirect to the previous page if valid
            if ($model->validate())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

	public function actionloginAffiliate() {
		if(isset($_GET["opauth"])) {
			try {
				$opauth_code = $_GET["opauth"];
				$response = json_decode(base64_decode($opauth_code), true);

				// Check if it's an error callback
				if (array_key_exists('error', $response) or !isset($response['auth'])) {
					MyLog::Error('Error get info!');
					$this->redirect('/');
				}

				$auth = $response['auth'];

				// Check if auth is missing info
				if(!isset($auth['provider']) or !isset($auth['uid']) or !isset($auth['info'])) {
					MyLog::Error('Cannot get auth info!');
					$this->redirect('/');
				}

				if(!in_array($auth['provider'], array('Facebook', 'Twitter', 'LinkedIn', 'Google', 'Orcid'))) {
					MyLog::Error('Provider is not supported!');
					$this->redirect('/');
				}

		        User::processAffiliateUser($auth);

				 #process to mark as logined in
				$_SESSION['affiliate_login']['provider'] = $auth['provider'];
				$_SESSION['affiliate_login']['uid'] = $auth['uid'];
				$_SESSION['affiliate_login']['token'] = $auth['credentials']['token'];

				#use useridentity to login
                $userIdentity = new AffiliateUserIdentity(
                                                $_SESSION['affiliate_login']['provider'],
                                                $_SESSION['affiliate_login']['uid']
                                );

				#complete authentication of affilate user
				if($userIdentity->authenticate()){
                    $duration= 3600*24*30 ; // 30 days
                    Yii::app()->user->login($userIdentity,$duration);
					$this->redirect(Yii::app()->user->returnUrl);
				} else {
					Yii::log("FAILED VALIDATION: " . $userIdentity->errorCode , "error");
				}

		        } catch (Exception $e) {
		                MyLog::error(print_r($e, true));
		                exit;
		        }
		} else {
			$this->redirect('/');
		}
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout() {
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	/**
	 * revoke  the current affiliatte user granting and redirect to homepage.
	 */
	public function actionRevoke() {
		AffiliateUserIdentity::revoke_token() ;
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

    public function actionSu() {
        $form = new SuLoginForm;
        if (isset($_POST['SuLoginForm'])) {
            $form->attributes = $_POST['SuLoginForm'];
            // validate user input and redirect to previous page if valid
            if ($form->validate()) {
                $this->redirect(Yii::app()->user->returnUrl);
            }
        }
        $this->render('su', array('form'=>$form)) ;
    }

    public function actionFeed(){
        header("Content-type: text/xml");
        echo Yii::app()->newsAndFeedsService->getRss();
        exit;
    }


}
