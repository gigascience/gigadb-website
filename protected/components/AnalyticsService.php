<?php

class AnalyticsService extends CApplicationComponent
{
    /** @var Google_Client $client Google API client */
    public $client;

    /**
     * @var string $client_email service account email associated with GigaDB project on Gigascience Google Analytics account */
    public $client_email;

    /**
     * @var string $client_id Key ID for a service account associated with GigaDB project on Gigascience Google Analytics account */
    public $client_id;

    /**
     * @var string $key_file path to a json file containing credentials for a service account associated with GigaDB project on Gigascience Google Analytics account */
    public $key_file;

    /** @var string $app_name name of the project (GigaDB) on Gigascience's Google Analytics account */
    public $app_name;

    public function init()
    {
        parent::init();
        $client = new Google_Client();
        $client->setApplicationName($this->app_name);
        $client->setDeveloperKey($this->client_id);
        $client->setSubject($this->client_email);
        $scopes = [ Google_Service_Analytics::ANALYTICS ];
        $client->setAuthConfig($this->key_file);
        $client->setScopes($scopes);
        $this->client = $client;
    }

    public function getFirstAccount(&$analytics)
    {

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

        if (!$profileID) {
            return null;
        }

        if (!$to) {
            $to = date('Y-m-d');
        }

        $filter = array();
        foreach ($paths as $path) {
            $filter[] = 'ga:pagePath' . $path;
        }
        $str = implode(',', $filter);

        $views = $ga->get(
            'ga:' . $profileID,
            $from,
            $to,
            'ga:pageviews,ga:sessions,ga:newUsers',
            array(
                        'dimensions' => 'ga:date',
                        'sort' => 'ga:date',
                        'filters' => $str
                    )
        );

        //Yii::log(print_r($views->rows, true), 'debug');

        return $views->rows;
    }
}
