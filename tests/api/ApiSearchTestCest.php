<?php

declare(strict_types=1);

/**
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:cest api ApiSearchTest
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run  api ApiSearchTestCest
 */
class ApiSearchTestCest
{
    public function tryToQueryDatasetsWithSamplesSorted(ApiTester$I, \Codeception\Module\Db $db)
    {
        $query = "SELECT d.identifier, d.upload_status
                  FROM Dataset d
                  LEFT JOIN Dataset_sample s ON d.id = s.dataset_id
                  WHERE d.upload_status = 'Published'
                  GROUP BY d.identifier, d.upload_status
                  HAVING COUNT(s.id) > 3";

        $identifier = $this->executeSqlQuery($query, $db)['identifier'];

        $I->sendGET(sprintf('/dataset?doi=%s&result=sample', $identifier));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsXml();

        $response = $I->grabResponse();
        $xml = simplexml_load_string(($response));

        $samples = [];

        foreach ($xml->xpath('//sample') as $sample) {
            $samples[] = (int) $sample['id'];
        }

        $sortedSamples = $samples;
        rsort($samples);

        $I->assertEquals($sortedSamples, $samples, 'not ordered');
    }

    public function tryToQueryDatasetsWithFilesSorted(ApiTester $I, \Codeception\Module\Db $db)
    {
        $query = "SELECT d.identifier, d.upload_status
                  FROM Dataset d
                  LEFT JOIN File s ON d.id = s.dataset_id
                  WHERE d.upload_status = 'Published'
                  GROUP BY d.identifier, d.upload_status
                  HAVING COUNT(s.id) > 3";

        $identifier = $this->executeSqlQuery($query, $db)['identifier'];

        $I->sendGET(sprintf('/dataset?doi=%s&result=file', $identifier));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsXml();

        $response = $I->grabResponse();
        $xml = simplexml_load_string(($response));


        $files = [];
        foreach ($xml->xpath('//file') as $file) {
            $files[] = (int) $file['id'];
        }

        $sortedFiles = $files;
        rsort($files);

        $I->assertEquals($sortedFiles, $files, 'not ordered');
    }

    private function executeSqlQuery($query, $db)
    {
        $dbh = $db->dbh;
        $stmt = $dbh->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
