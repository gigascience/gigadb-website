<?php

class SiteController extends Controller {
    /**
 	 * Declares class-based actions.
	 */
	public function actions() {
		return array(
			# captcha action renders the CAPTCHA image displayed on the contact page
			#'captcha'=>array(
			#	'class'=>'CCaptchaAction',
			#	'backColor'=>0xFFFFFF,
			#),
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
            array('allow', # admins
                'actions'=>array('admin'),
                'roles'=>array('admin'),
            ),
            array('deny',  // deny to admin action
                'users'=>array('admin'),
            ),
            array('allow',  // allow all users
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

		$form=new SearchForm;  // Use for Form
		$dataset = new Dataset; // Use for auto suggestion

		$datasetModel=$this->getDatasetByType(0);  // Use for image slider content

		$publicIds = Yii::app()->db->createCommand()
	                ->select("id")
	                ->from("dataset")
	                ->where("upload_status = 'Published'")
	                ->queryAll();

		$datasettypes_hints = Type::model()->findAll(array('order'=>'name ASC'));

		$news = News::model()->findAll("start_date<=current_date AND end_date>=current_date");


		$criteria=new CDbCriteria;
		$criteria->limit = 10;
		$criteria->condition = "upload_status = 'Published'";
		#$criteria->order = "id DESC";
		$criteria->order = 'publication_date DESC';
		$latest_datasets = Dataset::model()->findAll($criteria);

		$criteria->condition = null;
		$criteria->order = 'publication_date DESC';
		$latest_messages = RssMessage::model()->findAll($criteria);

		$rss_arr = array_merge($latest_datasets , $latest_messages);

		$this->sortRssArray($rss_arr);

		$this->render('index',array(
			'datasets'=>$datasetModel,
			'form'=>$form,
			'dataset'=>$dataset,
			'news'=>$news,
			'dataset_hint'=>$datasettypes_hints ,
			'rss_arr' => $rss_arr ,
			'count' => count($publicIds),
			'latest_datasets'=>$latest_datasets)
		);
	}

    private function sortRssArray(&$rss_arr){
        //Using Bubble Sort
        while(True){
            $swapped = False ;
            for($i = 0 ; $i < count($rss_arr) - 1 ; ++$i){
                if($rss_arr[$i]->publication_date < $rss_arr[$i+1]->publication_date){
                    $temp = $rss_arr[$i+1];
                    $rss_arr[$i+1] = $rss_arr[$i];
                    $rss_arr[$i] = $temp;
                    $swapped = True;
                }
            }
            if(!$swapped)
                break;
        }
    }

	private function loadUser() {
	  return User::model()->findbyPk(Yii::app()->user->_id);
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
	 * Displays the contact page
	 */
	public function actionContact() {
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

	public function actionAbout() {
		$this->render('about');
	}
        
    public function actionAdvisory() {
		$this->render('advisory');
	}
	public function actionFaq() {
		$this->render('faq');
	}

	public function actionTerm() {
		$this->render('term');
	}


	public function actionHelp() {
		$this->render('help');
	}


	public function actionPrivacy() {
		$this->render('privacy');
	}

    public function getDatasetByType($type) {

 	if ($type > 0) {
        $models = Dataset::model()->findAllBySql("SELECT * FROM dataset JOIN dataset_type ON dataset.id=dataset_type.dataset_id WHERE dataset_type.type_id=:type_id AND dataset.upload_status = 'Published' limit 9", array(':type_id' => $type));
        } else {
            $models = Dataset::model()->findAllBySql("SELECT * FROM dataset WHERE dataset.upload_status = 'Published' limit 9");
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
		// collect user input data
		if (isset($_POST['LoginForm'])) {
			$model->attributes=$_POST['LoginForm'];
            		$model->username = strtolower($_POST['LoginForm']['username']);
			// validate user input and redirect to the previous page if valid
			if($model->validate())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	public function actionChooseLogin() {
		$this->render('chooseLogin');
	}

	public function actionloginAffiliate() {
		if(isset($_GET["opauth"])) {
			try {
				$opauth_code = $_GET["opauth"];
				$response = unserialize(base64_decode($opauth_code));

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

		                 $user = User::processAffiliateUser($auth);

				 #process to mark as logined in
				$_SESSION['affiliate_login']['provider'] = $auth['provider'];
				$_SESSION['affiliate_login']['uid'] = $auth['uid'];

				#use useridentity to login
				$model = new LoginForm;
				$model->username = $auth['uid'];
				$model->password = $auth['uid'];

				#validate user input and redirect to the previous page if valid
				if($model->validate()){
					$this->redirect(Yii::app()->user->returnUrl);
				} else {
					Yii::log("FAILED VALIDATION: " . print_r($model->getErrors(), true) , "error");
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
	Yii::import('ext.feed.*');

	// specify feed type
	$feed = new EFeed(EFeed::RSS1);
	$feed->title = 'Testing the RSS 1 EFeed class';
	$feed->link = 'http://www.ramirezcobos.com';
	$feed->description = 'This is test of creating a RSS 1.0 feed by Universal Feed Writer';
	$feed->RSS1ChannelAbout = 'http://www.ramirezcobos.com/about';
	// create our item
	$item = $feed->createNewItem();
	$item->title = 'The first feed';
	$item->link = 'http://www.yiiframework.com';
	$item->date = time();
	$item->description = 'Amaz-ii-ng <b>Yii Framework</b>';
	$item->addTag('dc:subject', 'Subject Testing');

	$feed->addItem($item);

	$feed->generateFeed();
      }

    public function actionChangeLanguage() {
        /* Change the session's language if the requested language is
         * supported */
        Utils::changeLanguage(Utils::get($_GET, 'lang'));

        /* Return to the previous page */
        $returnUrl = Yii::app()->request->urlReferrer;
        if (!$returnUrl)
            $returnUrl = '/';
        $this->redirect($returnUrl);
    }


}
