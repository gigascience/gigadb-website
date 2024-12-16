<?php

declare(strict_types=1);

// docker-compose run --rm application ./protected/yiic updatedatasetdatacite --limit --offset --doi
class UpdateDatasetDataciteCommand extends CConsoleCommand
{
    private ?\GuzzleHttp\Client $client = null;
    private array $options = [];
    private $db = null;

    public function init()
    {
        parent::init();
        $this->client = new \GuzzleHttp\Client();
        $mds_username = Yii::app()->params['mds_username'];
        $mds_password = Yii::app()->params['mds_password'];
        $this->options = [
            'headers'     => [
                'Content-Type' => 'text/xml;charset=UTF8',
            ],
            'auth'        => [$mds_username, $mds_password],
            'http_errors' => false
        ];
        $this->db = $db = Yii::app()->db;
    }

    public function actionIndex($batchSize = 50, $offset = 0, $doi = null) {
        $mds_metadata_url= Yii::app()->params['mds_metadata_url'];
        $mds_prefix = Yii::app()->params['mds_prefix'];
        $count = 0;

        while(true) {
            $criteria = new CDbCriteria();
            $criteria->addCondition("upload_status = 'Published'");
            if ($doi) {
                $criteria->addCondition("identifier = :doi");
                $criteria->params = [':doi' => $doi];
            }
            $criteria->limit = $batchSize;
            $criteria->offset = $offset;

            $datasets = Dataset::model()->findAll($criteria);

            if (!$datasets) {
                break;
            }

            $this->processBatch($datasets, $mds_metadata_url, $mds_prefix);
            $offset += $batchSize;
        }
    }

    private function processBatch($datasets, $mds_metadata_url, $mds_prefix) {
        $promises = [];
        if (!$this->options) {
            Yii::log('no options defined', 'info');

            return;
        }

        $xmlByIds = [];
        $i = 0;
        foreach ($datasets as $dataset) {
            $xmlData = $dataset->toXml();

            if (!$xmlData) {
                Yii::log(sprintf('empty xml for dataset %s', $dataset->identifier), 'info');
                continue;
            }

            $options['body'] = $xmlData;

            $promises[] = $this->client->postAsync($mds_metadata_url . '/' . $mds_prefix . '/' . $dataset->identifier, $this->options)->then(
                function ($response) use ($dataset, $xmlData, $xmlByIds, $i) {
                    if ($response->getStatusCode() === 201) {
                        $xmlByIds[] = [$dataset->id, $xmlData];

                        return $xmlByIds;
                    }

                    Yii::log(sprintf('call datacite api returns %s for dataset %s', $response->getStatusCode(), $dataset->identifier), 'info');
                },
                function ($exception) use ($dataset) {
                    Yii::log(sprintf('call api - exception for dataset %s: %s', $dataset->identifier, $exception->getMessage()), 'info');
                }
            );

            $i++;
        }

        try {
            $xmlByIds = \GuzzleHttp\Promise\Utils::settle($promises)->wait();
            $this->updateEntityStatus($xmlByIds);
        } catch(\Exception $e) {
            Yii::log('Error while handling promises %s: %s', 'error');
        }
    }

    private function updateEntityStatus($xmlByIds) {
        $values = '';
        $count = count($xmlByIds);
        $sql = '';
        $params = [];

        foreach ($xmlByIds as $k => $xmlById) {
            if (is_null($xmlById['value'])) {
                Yii::log($xmlById['state'], 'info');
                continue;
            }

            $key = $xmlById['value'][0][0];
            $xml = $xmlById['value'][0][1];
            $sql .= sprintf('(:id%s, :action%s, :comments%s)%s', $key, $key, $key, $k + 1 !== $count ? ',' : '');
            $params += [":id$key" => $key, ":action$key" => 'xml', ":comments$key" => $xml];
        }

        if (!$sql) {
            return;
        }

        $sql = rtrim($sql, ',');
        $sql = 'INSERT INTO curation_log (dataset_id, action, comments) VALUES ' . $sql;
        $command = $this->db->createCommand($sql);
        $command->execute($params);
    }
}
