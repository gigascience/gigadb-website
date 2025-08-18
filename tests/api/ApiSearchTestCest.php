<?php

declare(strict_types=1);

/**
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:cest api ApiSearchTest
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run  api ApiSearchTestCest
 */
class ApiSearchTestCest
{
    public function tryToQueryADatasetWithAValidXml(ApiTester$I)
    {
        $response = $I->sendGET('/dataset?doi=100006');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $I->assertStringStartsWith("<?xml", $I->grabResponse());
    }

    public function tryToQueryASingleDatasetOnly(ApiTester $I)
    {
        $response = $I->sendGET('/dataset?doi=100006&result=dataset');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $I->assertStringStartsWith('<?xml', $I->grabResponse());
        $response = simplexml_load_string($I->grabResponse());

        $I->assertTrue(isset($response->dataset));
        $I->assertFalse(isset($response->samples));
        $I->assertFalse(isset($response->files));
    }

    public function tryToQueryADatasetAsAll(ApiTester $I)
    {
        $response = $I->sendGET('/dataset?doi=100006');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $I->assertStringStartsWith('<?xml', $I->grabResponse());
        $response = simplexml_load_string($I->grabResponse());

        $I->assertTrue(isset($response->dataset));
        $I->assertTrue(isset($response->samples));
        $I->assertTrue(isset($response->files));
    }

    public function tryToQueryADatasetSamplesOnly(ApiTester $I)
    {
        $response = $I->sendGET('/dataset?doi=100006&result=sample');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $I->assertStringStartsWith('<?xml', $I->grabResponse());
        $response = simplexml_load_string($I->grabResponse());

        $samples = $response->samples;
        $I->assertFalse(isset($response->dataset));
        $I->assertTrue(isset($response->samples));
        $I->assertCount(1, $samples->sample);
        $I->assertFalse(isset($response->files));
        $I->assertEquals('154', $samples->sample[0]['id']);
        $I->assertCount(13, $samples->sample[0]->sample_attributes->attribute);
        $I->assertEquals("source material identifiers", $samples->sample[0]->sample_attributes->attribute[0]->key);
        $I->assertEquals('David Lambert & BGI', $samples->sample[0]->sample_attributes->attribute[0]->value);
        $I->assertEquals('', $samples->sample[0]->sample_attributes->attribute[0]->unit['id']);
    }

    public function tryToQueryADatasetFilesOnly(ApiTester $I)
    {
        $response = $I->sendGET('/dataset?doi=100006&result=file');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $I->assertStringStartsWith('<?xml', $I->grabResponse());
        $response = simplexml_load_string($I->grabResponse());

        $I->assertFalse(isset($response->dataset));
        $I->assertFalse(isset($response->samples));
        $files = $response->files;
        $I->assertTrue(isset($files));
        $I->assertCount(7, $files->file);
        $I->assertEquals("17681", $files->file[0]['id']);
        $I->assertEquals('17680', $files->file[1]['id']);
        $I->assertEquals('17679', $files->file[2]['id']);
        $I->assertEquals('17678', $files->file[3]['id']);
        $I->assertEquals('17677', $files->file[4]['id']);
        $I->assertEquals('664', $files->file[5]['id']);
        $I->assertEquals('663', $files->file[6]['id']);
        $I->assertCount(1, $files->file->linked_samples);
        $I->assertCount(1, $files->file->file_attributes);
        $I->assertEquals('MD5 checksum', $files->file->file_attributes->attribute->key);
    }

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
        $xml = simplexml_load_string($response);

        $samples = [];

        foreach ($xml->xpath('//sample') as $sample) {
            $samples[] = (int) $sample['id'];
        }

        $sortedSamples = $samples;
        rsort($samples);

        $I->assertEquals($sortedSamples, $samples, 'not ordered');
    }

    public function tryToQueryDatasetsWithSamplesAttributesSorted(ApiTester$I)
    {
        $I->sendGET('/dataset?doi=100006&result=sample');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsXml();

        $response = $I->grabResponse();
        $xml = simplexml_load_string(($response));

        $sample = $xml->xpath('//sample/sample_attributes')[0];
        $firstAttr = $sample->attribute[0];
        $secondAttr = $sample->attribute[1];

        $I->assertEquals('source material identifiers', (string) $firstAttr->key, 'not ordered');
        $I->assertEquals('estimated genome size', (string) $secondAttr->key, 'not ordered');
    }

    public function tryToQueryDatasetsWithFileAttributesSorted(ApiTester$I)
    {
        $I->sendGET('/dataset?doi=100245&result=file');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsXml();

        $response = $I->grabResponse();
        $xml = simplexml_load_string(($response));

        $file = $xml->xpath('//file[12]/file_attributes')[0];
        $firstAttr = $file[0]->attribute[0];
        $secondAttr = $file->attribute[1];

        $I->assertEquals('MD5 checksum', (string) $firstAttr->key, 'not ordered');
        $I->assertEquals('camera parameters', (string) $secondAttr->key, 'not ordered');
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
        $dbh = $db->_getDbh();
        $stmt = $dbh->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function tryToQueryListDatasetWithStartDateAndEndDate(ApiTester $I)
    {
        $response = $I->sendGET('/list?start_date=2011-07-06&end_date=2013-09-11');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $I->assertStringStartsWith('<?xml', $I->grabResponse());

        $response = $I->grabResponse();
        $xml = simplexml_load_string(($response));

        $I->assertCount(3, $xml->doi);
    }

    public function tryToQueryListDatasetWithOnlyStartDate(ApiTester $I)
    {
        $response = $I->sendGET('/list?start_date=2013-07-06');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $I->assertStringStartsWith('<?xml', $I->grabResponse());

        $response = $I->grabResponse();
        $xml = simplexml_load_string(($response));

        $I->assertCount(6, $xml->doi);
    }
}
