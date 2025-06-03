<?php

declare(strict_types=1);

/**
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:cest api ApiSearchTest
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run  api ApiSearchTestCest
 */
class ApiSearchTestCest
{
    public function tryToQueryASingleDatasetWithAValidXml(ApiTester$I)
    {
        $response = $I->sendGET('/dataset?doi=100006');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $I->assertStringStartsWith("<?xml", $I->grabResponse());
    }

    public function tryToQueryASingleDatasetOnlyWithAValidXml(ApiTester $I)
    {
        $response = $I->sendGET('/dataset?doi=100006&result=dataset');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $response = $I->grabResponse();
        $I->assertStringStartsWith('<?xml', $response);

        $responseAsSimpleXml = new SimpleXMLElement($response);
        $I->assertEquals('Genomic data from Adelie penguin (Pygoscelis adeliae). ', $responseAsSimpleXml->dataset->title);
        $I->assertNull($responseAsSimpleXml->samples->sample);
        $I->assertNull($responseAsSimpleXml->files->file);
    }

    public function tryToQueryASingleDatasetWithOutputSamplesOnly(ApiTester $I)
    {
        $response = $I->sendGET('/dataset?doi=100006&result=sample');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $response = $I->grabResponse();
        $I->assertStringStartsWith('<?xml', $response);

        $responseAsSimpleXml = new SimpleXMLElement($response);
        $I->assertEquals('Pygoscelis_adeliae', $responseAsSimpleXml->samples->sample[0]->name);
        $I->assertEquals('9238', $responseAsSimpleXml->samples->sample[0]->species->tax_id);
        $I->assertNull($responseAsSimpleXml->files->file);
        $I->assertNull($responseAsSimpleXml->dataset->title);
    }

    public function tryToQueryASingleDatasetWithOutputFilesOnly(ApiTester $I)
    {
        $response = $I->sendGET('/dataset?doi=100006&result=file');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $response = $I->grabResponse();
        $I->assertStringStartsWith('<?xml', $response);

        $responseAsSimpleXml = new SimpleXMLElement($response);
        $I->assertEquals('Pygoscelis_adeliae.scaf.fa.gz', $responseAsSimpleXml->files->file[5]->name);
        $I->assertNull($responseAsSimpleXml->samples->sample);
        $I->assertNull($responseAsSimpleXml->dataset->title);
    }

    public function tryToQueryASingleDatasetWithFullOutput(ApiTester $I)
    {
        $response = $I->sendGET('/dataset?doi=100006');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $response = $I->grabResponse();
        $I->assertStringStartsWith('<?xml', $response);

        $responseAsSimpleXml = new SimpleXMLElement($response);
        $I->assertEquals('Genomic data from Adelie penguin (Pygoscelis adeliae). ', $responseAsSimpleXml->dataset->title);
        $I->assertEquals('9238', $responseAsSimpleXml->samples->sample[0]->species->tax_id);
        $I->assertEquals('Pygoscelis_adeliae.scaf.fa.gz', $responseAsSimpleXml->files->file[5]->name);
    }

    public function tryToSearchWithKeywordAndOutputDatasetOnly(ApiTester $I)
    {
        $response = $I->sendGET('/search?keyword=description:Antartica');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $response = $I->grabResponse();
        $I->assertStringStartsWith('<?xml', $response);

        $responseAsSimpleXml = new SimpleXMLElement($response);
        $I->assertEquals('Genomic data from Adelie penguin (Pygoscelis adeliae). ', $responseAsSimpleXml->gigadb_entry->dataset->title);
        $I->assertNull($responseAsSimpleXml->gigadb_entry->samples->sample);
        $I->assertNull($responseAsSimpleXml->gigadb_entry->files->file);
    }

    public function tryToSearchWithKeywordAndOutputFileOnly(ApiTester $I)
    {
        $response = $I->sendGET('/search?keyword=description:Antartica&result=file');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $response = $I->grabResponse();
        $I->assertStringStartsWith('<?xml', $response);

        $responseAsSimpleXml = new SimpleXMLElement($response);
        $I->assertEquals('Pygoscelis_adeliae.scaf.fa.gz', $responseAsSimpleXml->gigadb_entry->files->file[5]->name);
        $I->assertNull($responseAsSimpleXml->gigadb_entry->samples->sample);
        $I->assertNull($responseAsSimpleXml->gigadb_entry->dataset->title);
    }

    public function tryToSearchWithKeywordAndOutputSampleOnly(ApiTester $I)
    {
        $response = $I->sendGET('/search?keyword=description:Antartica&result=sample');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsXml();
        $response = $I->grabResponse();
        $I->assertStringStartsWith('<?xml', $response);

        $responseAsSimpleXml = new SimpleXMLElement($response);
        $I->assertEquals('Pygoscelis_adeliae', $responseAsSimpleXml->gigadb_entry->samples->sample[0]->name);
        $I->assertNull($responseAsSimpleXml->gigadb_entry->files->file);
        $I->assertNull($responseAsSimpleXml->gigadb_entry->dataset->title);
    }


    public function tryToQueryDatasetsWithSamplesSorted(ApiTester $I, \Codeception\Module\Db $db)
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

    public function tryToQueryDatasetsWithSamplesAttributesSorted(ApiTester $I)
    {
        $I->sendGET('/dataset?doi=100006&result=sample');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsXml();

        $response = $I->grabResponse();
        $xml = simplexml_load_string(($response));

        $sample = $xml->xpath('//sample/sample_attributes')[0];
        $firstAttr = $sample->attribute[0];
        $secondAttr = $sample->attribute[1];

        $I->assertEquals('alternative names', (string) $firstAttr->key, 'not ordered');
        $I->assertEquals('tissue', (string) $secondAttr->key, 'not ordered');
    }

    public function tryToQueryDatasetsWithFileAttributesSorted(ApiTester $I)
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

        $I->assertCount(4, $xml->doi);
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
