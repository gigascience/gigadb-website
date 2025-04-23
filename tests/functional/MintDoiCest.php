<?php

/**
 * MintDoiCest: test the integration between the mint doi button and datacite sandbox api
 *
 *
 * generated with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept generate:cest functional MintDoiCest.php
 *
 * run with:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run functional MintDoiCest
 */
class MintDoiCest
{
    private $client;

    public function _before(FunctionalTester $I)
    {
        $this->client =  new \GuzzleHttp\Client();
    }

    public function getMDSConfig()
    {
        $config = require("/var/www/protected/config/local.php");

        return [
            'mds_doi_url' => $config['params']['mds_doi_url'],
            'mds_metadata_url' => $config['params']['mds_metadata_url'],
            'mds_username' => $config['params']['mds_username'],
            'mds_password' => $config['params']['mds_password'],
            'mds_prefix' => $config['params']['mds_prefix'],
            'invalid_username' => 'test.gigadb',
            'invalid_password' => 'testpassword'
        ];
    }

    // tests
    public function tryGetTheStatusReturnOfAnExistingDOI(FunctionalTester $I)
    {
        $mdsConfig = $this->getMDSConfig();
        $doi = 100006;

        $doiResponse = $this->client->request('GET', $mdsConfig['mds_doi_url'] . '/' . $mdsConfig['mds_prefix']  . '/' . $doi, [
            'http_errors' => false,
            'auth' => [$mdsConfig['mds_username'], $mdsConfig['mds_password']]
        ]);
        $response = $doiResponse->getBody()->getContents();
        $status = $doiResponse->getStatusCode();

        $I->assertEquals('http://gigadb.org/dataset/100006', $response);
        $I->assertEquals(200, $status);
    }

    public function tryGetTheStatusReturnOfNonExistingDOI (FunctionalTester $I)
    {
        $mdsConfig = $this->getMDSConfig();
        $doi = 999999;

        $doiResponse = $this->client->request('GET', $mdsConfig['mds_doi_url'] . '/' . $mdsConfig['mds_prefix']  . '/' . $doi, [
            'http_errors' => false,
            'auth' => [$mdsConfig['mds_username'], $mdsConfig['mds_password']]
        ]);
        $response = $doiResponse->getBody()->getContents();
        $status = $doiResponse->getStatusCode();

        $I->assertEquals('DOI not found', $response);
        $I->assertEquals(404, $status);
    }

    public function tryCreateDoiWhenNonExistMetadata(FunctionalTester $I)
    {
        $mdsConfig = $this->getMDSConfig();
        $doi = 999999;

        $doi_data = "doi=" .$mdsConfig['mds_prefix'] . "/" . $doi . "\n" . "url=http://gigadb.org/dataset/" . $doi;

        $options = [
            'headers' => [
                'Content-Type' => 'text/plain; charset=UTF-8',
            ],
            'auth'    => [$mdsConfig['mds_username'], $mdsConfig['mds_password']],
            'body'    => $doi_data,
            'http_errors' => false
        ];

        $response = $this->client->request('PUT', $mdsConfig['mds_doi_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi, $options);

        $createDoiResponse = $response->getBody()->getContents();
        $createDoiStatus = $response->getStatusCode();

        $I->assertEquals('Can\'t be blank', $createDoiResponse);
        $I->assertEquals(422, $createDoiStatus);
    }

    public function tryCreateExistingDOIWithMetadata(FunctionalTester $I)
    {
        $mdsConfig = $this->getMDSConfig();
        $doi = 100006;

        $data = simplexml_load_file('/var/www/tests/_data/test_100006.xml');

        $options = [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ],
            'auth'    => [$mdsConfig['mds_username'], $mdsConfig['mds_password']],
            'body'    => $data,
            'http_errors' => false
        ];
        $updateMdResponse = $this->client->request('POST', $mdsConfig['mds_metadata_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi, $options);

        $createMdResponse = $updateMdResponse->getBody()->getContents();
        $createMdStatus = $updateMdResponse->getStatusCode();

        $I->assertEquals("OK (10.80027/100006)", $createMdResponse);
        $I->assertEquals("201", $createMdStatus);

        $doi_data = "doi=" . $mdsConfig['mds_prefix'] . "/" . $doi . "\n" . "url=http://gigadb.org/dataset/" . $doi;

        $options = [
            'headers' => [
                'Content-Type' => 'text/plain; charset=UTF-8',
            ],
            'auth'    => [$mdsConfig['mds_username'], $mdsConfig['mds_password']],
            'body'    => $doi_data,
            'http_errors' => false
        ];

        $response = $this->client->request('PUT', $mdsConfig['mds_doi_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi, $options);

        $createDoiResponse = $response->getBody()->getContents();
        $createDoiStatus = $response->getStatusCode();

        $I->assertEquals("OK", $createDoiResponse);
        $I->assertEquals("201", $createDoiStatus);
    }

    /**
     * @skip as datacite mds api does not allow delete and then create a doi, need to create a fake mds api
     * TODO
     */
    public function tryCreateNonExistingDoiWithMetadata(FunctionalTester $I)
    {

    }

    public function tryCreateDoiWithInvalidCredentials(FunctionalTester $I)
    {
        $mdsConfig = $this->getMDSConfig();
        $doi = 100006;

        $data = simplexml_load_file('/var/www/tests/_data/test_100006.xml');

        $options = [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ],
            'auth'    => [$mdsConfig['invalid_username'], $mdsConfig['invalid_password']],
            'body'    => $data,
            'http_errors' => false
        ];
        $updateMdResponse = $this->client->request('POST', $mdsConfig['mds_metadata_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi, $options);

        $createMdResponse = $updateMdResponse->getBody()->getContents();
        $createMdStatus = $updateMdResponse->getStatusCode();

        $I->assertEquals("Bad credentials", $createMdResponse);
        $I->assertEquals("401", $createMdStatus);

        $doi_data = "doi=" . $mdsConfig['mds_prefix'] . "/" . $doi . "\n" . "url=http://gigadb.org/dataset/" . $doi;
        $options = [
            'headers' => [
                'Content-Type' => 'text/plain; charset=UTF-8',
            ],
            'auth'    => [$mdsConfig['invalid_username'], $mdsConfig['invalid_password']],
            'body'    => $doi_data,
            'http_errors' => false
        ];

        $response = $this->client->request('PUT', $mdsConfig['mds_doi_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi, $options);

        $createDoiResponse = $response->getBody()->getContents();
        $createDoiStatus = $response->getStatusCode();

        $I->assertEquals("Bad credentials", $createDoiResponse);
        $I->assertEquals("401", $createDoiStatus);
    }

    public function tryUpdateMetadataWithNotRecognizedXml(FunctionalTester $I)
    {
        $mdsConfig = $this->getMDSConfig();
        $doi = 100006;

        $data = new SimpleXMLElement("<resource></resource>");

        $options = [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ],
            'auth'    => [$mdsConfig['mds_username'], $mdsConfig['mds_password']],
            'body'    => $data->asXML(),
            'http_errors' => false
        ];
        $updateMdResponse = $this->client->request('POST', $mdsConfig['mds_metadata_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi, $options);

        $createMdResponse = $updateMdResponse->getBody()->getContents();
        $createMdStatus = $updateMdResponse->getStatusCode();

        $I->assertEquals("Metadata format not recognized", $createMdResponse);
        $I->assertEquals("415", $createMdStatus);
    }

    public function tryUpdateMetadataWithInvalidXml(FunctionalTester $I)
    {
        $mdsConfig = $this->getMDSConfig();
        $doi = 100006;

        $data = simplexml_load_file('/var/www/tests/_data/test_invalid.xml');

        $options = [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ],
            'auth'    => [$mdsConfig['mds_username'], $mdsConfig['mds_password']],
            'body'    => $data->asXML(),
            'http_errors' => false
        ];
        $updateMdResponse = $this->client->request('POST', $mdsConfig['mds_metadata_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi, $options);

        $createMdResponse = $updateMdResponse->getBody()->getContents();
        $createMdStatus = $updateMdResponse->getStatusCode();

        $I->assertContains("DOI 10.80027/100006: Missing child element(s). Expected is one of ( {http://datacite.org/schema/kernel-4}creators", $createMdResponse);
        $I->assertEquals("422", $createMdStatus);
    }
}
