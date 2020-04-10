<?php

class SiteController extends Controller {
    /**
 	 * Declares class-based actions.
	 */
    //public $layout='//layouts/new_main';
	public function actions() {
		return array(
			# captcha action renders the CAPTCHA image displayed on the contact page
			// 'captcha'=>array(
			// 	'class'=>'CCaptchaAction',
			// 	'backColor'=>0xFFFFFF,
			// ),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
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
            ),
            array('allow', # admins
                'actions'=>array('admin', 'su'),
                'roles'=>array('admin'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
    *
    * Administration action
	*
	**/

	public function actionAdmin() {
		$this->render('admin');
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex() {
	#if (Yii::app()->user->isGuest) {
	#    $this->render('index');
	#} else {
	#    if (Yii::app()->user->checkAccess('admin')) {
	#        $this->redirect(array('admin/index'));
	#    } else {
	#        $this->redirect(array('user/accountBalance', 'id'=>Yii::app()->user->_id));
	#    }
	#}
                $this->layout='new_main';
		$form=new SearchForm;  // Use for Form
		$dataset = new Dataset; // Use for auto suggestion

		$datasetModel=$this->getDatasetByType(0);  // Use for image slider content

		$publicIds = Yii::app()->db->createCommand()
	                ->select("id")
	                ->from("dataset")
	                ->where("upload_status = 'Published'")
	                ->queryAll();

		$datasettypes_hints = Type::model()->findAll(array('order'=>'name ASC'));

        $news = Yii::app()->newsAndFeedsService->getTodaysNews();

        $rss_arr = Yii::app()->newsAndFeedsService->getFeedsData();

        //Get dataset types number
        $sql_1="select * from homepage_dataset_type";
        $command = Yii::app()->db->createCommand($sql_1);
        $results = $command->queryAll();

        $sql_2="select * from sample_number";
        $command = Yii::app()->db->createCommand($sql_2);
        $count_sample = $command->queryAll();

        $sql_3="select * from file_number";
        $command = Yii::app()->db->createCommand($sql_3);
        $count_file = $command->queryAll();

        $number_genome_mapping=0;
        $number_ecology=0;
        $number_eeg=0;
        $number_epi=0;
        $number_genomic=0;
        $number_imaging=0;
        $number_lipi=0;
        $number_metabarcoding=0;
        $number_metagenomic=0;
        $number_metadata=0;
        $number_metabolomic=0;
        $number_climate=0;
        $number_na=0;
        $number_ns=0;
        $number_pt=0;
        $number_proteomic=0;
        $number_software=0;
        $number_ts=0;
        $number_vm=0;
        $number_wf=0;

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
			'form'=>$form,
			'dataset'=>$dataset,
			'news'=>$news,
			'dataset_hint'=>$datasettypes_hints ,
			'rss_arr' => $rss_arr ,
			'count' => count($publicIds),
                        'count_sample' => $count_sample[0]['count'],
                        'count_file' => $count_file[0]['count'],
                        'number_genome_mapping'=>$number_genome_mapping,
                        'number_climate' => $number_climate,
                        'number_ecology'=>$number_ecology,
                        'number_eeg'=>$number_eeg,
                        'number_epi'=>$number_epi,
                        'number_genomic'=>$number_genomic,
                        'number_imaging'=>$number_imaging,
                        'number_lipi'=>$number_lipi,
                        'number_metabarcoding'=>$number_metabarcoding,
                        'number_metabolomic'=>$number_metabolomic,
                        'number_metadata'=>$number_metadata,
                        'number_metagenomic'=>$number_metagenomic,
                        'number_na'=>$number_na,
                        'number_ns'=>$number_ns,
                        'number_pt'=>$number_pt,
                        'number_proteomic'=>$number_proteomic,
                        'number_software'=>$number_software,
                        'number_ts'=>$number_ts,
                        'number_vm'=>$number_vm,
                        'number_wf'=>$number_wf,

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

    public function actionGuide() {
        $this->layout='new_main';
        $this->render('guide');
    }
    public function actionGuidegenomic() {
        $this->layout='new_main';
        $this->render('guidegenomic');
    }

    public function actionGuideimaging() {
        $this->layout='new_main';
        $this->render('guideimaging');
    }

    public function actionGuidemetabolomic() {
        $this->layout='new_main';
        $this->render('guidemetabolomic');
    }

    public function actionGuideepigenomic() {
        $this->layout='new_main';
        $this->render('guideepigenomic');
    }

    public function actionGuidemetagenomic() {
        $this->layout='new_main';
        $this->render('guidemetagenomic');
    }

    public function actionGuidesoftware() {
        $this->layout='new_main';
        $this->render('guidesoftware');
    }

	/**
	 * Displays the contact page
	 */
	public function actionContact() {

            $this->layout='new_main';
		$model = new ContactForm;
		if (isset($_POST['ContactForm'])) {
			$model->attributes=$_POST['ContactForm'];
			if ($model->validate()) {
				$headers = "From: {$model->name}<{$model->email}>\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
        Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}
	/**
	*This method returns all dataset locations
	*/
	public function actionMapbrowse() {

             $this->layout='new_main';
	     $locations = Yii::app()->db->createCommand("SELECT d.identifier,  d.title, satt.value, sp.scientific_name as sciname, s.id as sampleid FROM dataset as d
					      INNER JOIN dataset_sample as dsam on dsam.dataset_id = d.id
						  INNER JOIN sample as s on s.id = dsam.sample_id
					      INNER JOIN sample_attribute as satt on satt.sample_id=s.id
						  INNER JOIN species as sp on sp.id = s.species_id
						  where satt.attribute_id = 269 and d.upload_status='Published' order by sampleid")
                                              ->queryAll();

                foreach ($locations as $location) {

        $locationValue = $location["value"];
        $locationValue = preg_replace('/\s+/', '', $locationValue);
        $formatCheck = preg_match('/-?[0-9]*[.][0-9]*[,]-?[0-9]*[.][0-9]*/',$locationValue);
        if (!$formatCheck==1){
          continue;
        }
        $val = explode(',', $locationValue);
        if(strpos($val[0],'.') == false || !is_numeric($val[0])){
            continue;
        }
        if(strpos($val[1],'.') == false || !is_numeric($val[1])){
            continue;
        }
        $location["sciname"]=str_replace(",","",$location["sciname"]);




  }


            $this->render('mapbrowse', array('locations' => $locations));
	}

        public function actionTeam() {
                $this->layout='new_main';
		$this->render('team');
	}


	public function actionAbout() {
                $this->layout='new_main';
		$this->render('about');
	}

    public function actionAdvisory() {
                $this->layout='new_main';
		$this->render('advisory');
	}
	public function actionFaq() {
                $this->layout='new_faq';
		$this->render('faq');
	}

	public function actionTerm() {
                $this->layout='new_main';
		$this->render('term');
	}


	public function actionHelp() {
                $this->layout='new_main';
		$this->render('help');
	}


	public function actionPrivacy() {
		$this->render('privacy');
	}

    public function getDatasetByType($type) {

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

        $this->layout = "new_main";
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
                ## log su
                #$u= new ActiveRecordLog;
                #$u->description=  'User ' . Yii::app()->user->Name . ' LOGIN ';
                #$u->action=       'LOGIN';
                #$u->creationdate= date('Y-m-d H:i:s');
                #$u->userid=       Yii::app()->user->id;
                #$u->save();
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

    /**
	* This method generate captcha image
    */
    public function captchaGenerator($length = 7){
		try{
    		$im = imagecreatetruecolor(600, 100);
    		// Create some colors
    		$white = imagecolorallocate($im, 255, 255, 255);
    		// $grey = imagecolorallocate($im, 128, 128, 128);

    		$black = imagecolorallocate($im, 66, 164, 244);
    		imagefilledrectangle($im, 0, 0, 600, 100, $white);
    		// The text to draw

    		$text = Yii::$app->security->generateRandomString($length);
    		$font = '/fonts/times_new_yorker.ttf';
    		imagettftext($im, 70, 0, 20, 80, $black, $font, $text);

    		imagepng($im, 'images/tempcaptcha/'.$text.".png");
    		imagedestroy($im);
    		$_SESSION["captcha"] = $text;
    		return $text;
	}catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
}

}
