<?php

declare(strict_types=1);

// docker-compose run --rm application ./protected/yiic updatedatasetdatacite --limit --offset --doi > my_log.txt 2>&1
class UpdateDatasetDataciteCommand extends CConsoleCommand
{
    private ?\GuzzleHttp\Client $client = null;
    private array $options = [];
    private $db = null;
    private bool $hasError = false;

    public function init()
    {
        parent::init();
        $this->client = new \GuzzleHttp\Client();
        $this->options = [
            'headers'     => [
                'Content-Type' => 'text/xml;charset=UTF8',
            ],
            'auth'        => [
                Yii::app()->params['mds_username'],
                Yii::app()->params['mds_password']
            ],
            'http_errors' => false
        ];
        $this->db = Yii::app()->db;
    }

    public function actionIndex($batchSize = 50, $offset = 0, $doi = null)
    {
        $mds_metadata_url = Yii::app()->params['mds_metadata_url'];
        $mds_prefix = Yii::app()->params['mds_prefix'];

        $processed = 0;

        while (true) {
            $criteria = new CDbCriteria();
            $criteria->addCondition("upload_status = 'Published'");
            if ($doi) {
                $criteria->addCondition('identifier = :doi');
                $criteria->params = [':doi' => $doi];
            }
            $criteria->limit = $batchSize;
            $criteria->offset = $offset;

            $datasets = Dataset::model()->findAll($criteria);

            if (!$datasets) {
                break;
            }

            $count = count($datasets);
            fwrite(STDOUT, sprintf("Processing %d datasets \n", $count));
            $this->processBatch($datasets, $mds_metadata_url, $mds_prefix);
            $offset += $batchSize;
            $processed += $count;
        }

        fwrite(STDOUT, sprintf("Finished processing %d datasets\n", $processed));

        if ($this->hasError) {
            fwrite(STDERR, "Some errors occurred. See details above.\n");
        } else {
            fwrite(STDOUT, "All datasets processed successfully.\n");
        }
    }

    private function processBatch($datasets, $mds_metadata_url, $mds_prefix)
    {
        $promises = [];
        foreach ($datasets as $dataset) {

            $xmlData = $dataset->toXml();
            if (!$xmlData) {
                $this->hasError = true;
                fwrite(STDERR, sprintf("[ERROR] Empty XML for dataset: %s\n", $dataset->identifier));

                continue;
            }

            $url = $mds_metadata_url . '/' . $mds_prefix . '/' . $dataset->identifier;
            $options = $this->options;
            $options['body'] = $xmlData;

            $promises[] = $this->client->postAsync($url, $options)->then(
                function ($response) use ($dataset, $xmlData) {
                    $code = $response->getStatusCode();
                    $message = $response->getBody()->getContents();

                    if ($code !== 201) {
                        $this->hasError = true;
                        fwrite(STDERR, sprintf("[ERROR] Failed for dataset %s: %d - %s\n", $dataset->identifier, $code, $message));

                        return [
                            $dataset->id,
                            $xmlData,
                            'Failed to send DataCite XML',
                            $code,
                            $message
                        ];
                    }

                    fwrite(STDOUT, sprintf("[OK] Dataset %s successfully updated.\n", $dataset->identifier));

                    return [
                        $dataset->id,
                        $xmlData,
                        'Sent DataCite XML',
                        $code,
                        $message
                    ];
                },
                function ($e) use ($dataset) {
                    $this->hasError = true;
                    fwrite(STDERR, sprintf("[ERROR] Exception for dataset %s: %s\n", $dataset->identifier, $e->getMessage()));

                    return null;
                }
            );
        }

        try {
            $results = \GuzzleHttp\Promise\Utils::settle($promises)->wait();
            $this->updateEntityStatus($results);
        } catch (\Exception $e) {
            $this->hasError = true;
            fwrite(STDERR, '[CRITICAL] Error while waiting for promises: ' . $e->getMessage() . "\n");
        }
    }

    private function updateEntityStatus(array $xmlByIds)
    {
        $params = [];
        $params2 = [];
        $sql = '';
        $sql2 = '';

        foreach ($xmlByIds as $entry) {
            if (!isset($entry['value'])) {
                continue;
            }

            list($id, $xml, $action, $code, $message) = $entry['value'];

            $sql .= "(:id$id, :action$id, :comments$id),";
            $params += [
                ":id$id"       => $id,
                ":action$id"   => $action,
                ":comments$id" => $xml
            ];

            $sql2 .= "(:cid$id, :cmessage$id),";
            $params2 += [
                ":cid$id"       => $id,
                ":cmessage$id"   => sprintf('DOI Minting - Metadata response: %s - %s', $code, $message),
            ];
        }

        if ($sql) {
            $sql = rtrim($sql, ',');
            $command = $this->db->createCommand("INSERT INTO curation_log (dataset_id, action, comments) VALUES $sql");
            $command->execute($params);
        }

        if ($sql2) {
            $sql2 = rtrim($sql2, ',');
            $command = $this->db->createCommand("INSERT INTO dataset_log (dataset_id, message) VALUES $sql2");
            $command->execute($params2);
        }
    }
}
