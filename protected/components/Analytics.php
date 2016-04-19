<?php

require_once(Yii::getPathOfAlias('googleAPI') . '/Google/Client.php');
require_once(Yii::getPathOfAlias('googleAPI') . '/Google/Service/Analytics.php');
require_once(Yii::getPathOfAlias('googleAPI') . '/Google/Auth/AssertionCredentials.php');

class Analytics extends CApplicationComponent {	
	
	public $client;

	public $client_email;
	public $client_id;
	public $keyfile;
	public $app_name;

	public function init() {
		parent::init();		
		// api dependencies		
		$client = new Google_Client();
		$client->setClassConfig('Google_Cache_File', 'directory',realpath(dirname(__FILE__). '/../../giga_cache'));
		$client->setApplicationName($this->app_name);
		$client->setScopes(Google_Service_Analytics::ANALYTICS);

		$client->setAssertionCredentials(new Google_Auth_AssertionCredentials(
			$this->client_email,
			array(Google_Service_Analytics::ANALYTICS),
			file_get_contents($this->keyfile)
		));
		$client->setClientID($this->client_id);
		$this->client = $client;

	}

	public function getFirstAccount(&$analytics) {

		$accounts = $analytics->management_accounts->listManagementAccounts();	

		if (count($accounts->getItems()) > 0) {
	    	$items = $accounts->getItems();
	    	$firstAccountId = $items[0]->getId();

	    	$webproperties = $analytics->management_webproperties
	        					->listManagementWebproperties($firstAccountId);

		    if (count($webproperties->getItems()) > 0) {
		      	$items = $webproperties->getItems();
		      	$firstWebpropertyId = $items[0]->getId();

		      	$profiles = $analytics->management_profiles
		          		->listManagementProfiles($firstAccountId, $firstWebpropertyId);

		      	if (count($profiles->getItems()) > 0) {
		        	$items = $profiles->getItems();
		        	//Yii::log(print_r($items, true), 'debug');		        	
		        	return $items[0]->getId();
		        }
		    }
		}		      

		return '';
	}

	public function getAnalyticsData($from = '', $to = '', $paths = array()) 
	{
		
		$client = $this->client;
		$service = new Google_Service_Analytics($client);				
		$ga = $service->data_ga;

		$profileID = $this->getFirstAccount($service);
		
		if(!$profileID)
			return null;

		if(!$to)
			$to = date('Y-m-d');

		$filter = array();
		foreach($paths as $path) {
			$filter[] = 'ga:pagePath'.$path;
		}
		$str = implode(',', $filter);
		
		$views = $ga->get('ga:'.$profileID,
			        $from,
			        $to,
			        'ga:pageviews,ga:sessions,ga:newUsers',
			        array(
			            'dimensions' => 'ga:date',			           
			            'sort' => 'ga:date',			        
			            'filters' => $str
			        )); 

		//Yii::log(print_r($views->rows, true), 'debug');

		return $views->rows;
	}
	
}