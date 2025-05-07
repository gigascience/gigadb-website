<?php

declare(strict_types=1);

class CheckDoiExistsInDataciteApiCommand extends CConsoleCommand
{
    private bool $hasError = false;

    public function actionIndex(int $batchSize = 50, int $offset = 0, $doi = null)
    {
        $batchSize = $batchSize >= 450 ?  450 : $batchSize;
        $throttleLimit = 450;
        $throttleWindow = 300;

        $mds_doi_url = Yii::app()->params['mds_doi_url'];
        $mds_username = Yii::app()->params['mds_username'];
        $mds_password = Yii::app()->params['mds_password'];
        $mds_prefix = Yii::app()->params['mds_prefix'];
        $client = new \GuzzleHttp\Client();

        $condition = $doi
            ? ['upload_status' => 'Published', 'identifier' => $doi]
            : ['upload_status' => 'Published'];

        $processed = 0;
        $sentInWindow = 0;
        $windowStart = time();

        while (true) {
            $criteria = new CDbCriteria();
            foreach($condition as $k => $v) {
                $criteria->addCondition("$k = :$k");
                $criteria->params[":$k"] = $v;
            }
            $criteria->limit = $batchSize;
            $criteria->offset = $offset;
            $criteria->order = 'identifier DESC';

            $datasets = Dataset::model()->findAll($criteria);

            if (!$datasets) {
                fwrite(STDOUT, "No datasets found.\n");

                break;
            }

            $count = count($datasets);
            fwrite(STDOUT, sprintf("Processing batch of %d datasets with offset %d\n", $count, $offset));

            $options = [
                'http_errors' => false,
                'auth'        => [$mds_username, $mds_password],
            ];

            $promises = [];

            foreach ($datasets as $dataset) {
                $url = $mds_doi_url . '/' . $mds_prefix . '/' . $dataset->identifier;

                $promises[] = $client->getAsync($url, $options)->then(
                    function ($response) use ($dataset, $url) {
                        $code = $response->getStatusCode();

                        if (!in_array($code, [200, 204])) {
                            $this->hasError = true;
                            fwrite(STDERR, sprintf("[ERROR] DOI not found for dataset %s: %s\n", $dataset->identifier, $code));
                        } else {
                            fwrite(STDOUT, sprintf("[OK] DOI exists for dataset %s\n", $dataset->identifier));
                        }
                    },
                    function ($exception) use ($dataset, $url) {
                        $this->hasError = true;
                        fwrite(STDERR, sprintf("[ERROR] Error checking DOI for dataset %s: %s\n", $dataset->identifier, $exception->getMessage()));
                    }
                );
            }

            try {
                \GuzzleHttp\Promise\Utils::settle($promises)->wait();
            } catch (\Exception $e) {
                $this->hasError = true;
                fwrite(STDERR, '[ERROR] Error during request promises: ' . $e->getMessage() . "\n");
            }


            $sentInWindow += $count;
            $processed    += $count;
            $offset       += $batchSize;

            if ($sentInWindow >= $throttleLimit) {
                $elapsed = time() - $windowStart;
                if ($elapsed < $throttleWindow) {
                    $sleep = $throttleWindow - $elapsed;
                    fwrite(STDOUT, sprintf("[THROTTLE] reached %d requests; sleeping %d seconds\n", $throttleLimit, $sleep));
                    sleep($sleep);
                }
                $windowStart  = time();
                $sentInWindow = 0;
            }


            if ($this->hasError) {
                fwrite(STDERR, "[ERROR] Some errors were detected. Please check details above.\n");
            } else {
                fwrite(STDOUT, "[OK] All DOIs are correctly found in the DataCite API.\n");
            }
        }
    }
}
