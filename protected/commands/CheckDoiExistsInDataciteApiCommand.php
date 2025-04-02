<?php

declare(strict_types=1);

class CheckDoiExistsInDataciteApiCommand extends CConsoleCommand
{
    private bool $hasError = false;

    public function actionIndex($doi = null)
    {
        $mds_doi_url = Yii::app()->params['mds_doi_url'];
        $mds_username = Yii::app()->params['mds_username'];
        $mds_password = Yii::app()->params['mds_password'];
        $mds_prefix = Yii::app()->params['mds_prefix'];
        $client = new \GuzzleHttp\Client();
        $promises = [];
        $errors = [];

        $condition = $doi ? ['upload_status' => 'Published', 'identifier' => $doi] : ['upload_status' => 'Published'];
        $datasets = Dataset::model()->findAllByAttributes($condition);

        $options = [
            'http_errors' => false,
            'auth'        => [$mds_username, $mds_password]
        ];

        $var = 0;
        foreach ($datasets as $dataset) {
            $promises[] = $client->getAsync($mds_doi_url . '/' . $mds_prefix . '/' . $dataset->identifier, $options)->then(
                function ($response) use ($dataset) {
                    if (!in_array($response->getStatusCode(), [200, 204])) {
                        $this->hasError = true;
                        Yii::log(sprintf('DOI not found for dataset %s', $dataset->identifier), 'info');
                    }
                },
                function ($exception) use ($dataset, $errors) {
                    $errors[$dataset->id] = $dataset->identifier;
                    $this->hasError = true;
                    Yii::log(sprintf('Error while checking DOI for dataset %s: %s', $dataset->identifier, $exception->getMessage()), 'error');
                }
            );
            $var++;
        }

        try {
            \GuzzleHttp\Promise\Utils::settle($promises)->wait();
        } catch(\Exception $e) {
            $this->hasError = true;
            Yii::log('Error while handling promises %s: %s', 'error');
        }

        if ($this->hasError) {
            echo 'check the logs: some errors have been detected';
        } else {
            echo 'All good';
        }
    }
}
