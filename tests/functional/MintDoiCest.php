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
    public function _before(FunctionalTester $I)
    {
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
        $result = [];

        $checkDoi = curl_init();
        curl_setopt($checkDoi, CURLOPT_URL, $mdsConfig['mds_doi_url'] . '/' . $mdsConfig['mds_prefix']  . '/' . $doi);
        curl_setopt($checkDoi, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($checkDoi, CURLOPT_USERPWD, $mdsConfig['mds_username'] . ":" . $mdsConfig['mds_password']);
        $checkDoiResponse = curl_exec($checkDoi);
        $result['doi_response'] = $checkDoiResponse;
        $result['check_doi_status'] = curl_getinfo($checkDoi, CURLINFO_HTTP_CODE);
        curl_close($checkDoi);

        $I->assertEquals('http://gigadb.org/dataset/100006', $result['doi_response']);
        $I->assertEquals(200, $result['check_doi_status']);

        $checkMeta = curl_init();
        curl_setopt($checkMeta, CURLOPT_URL, $mdsConfig['mds_metadata_url'] . '/' . $mdsConfig['mds_prefix']  . '/' . $doi);
        curl_setopt($checkMeta, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($checkMeta, CURLOPT_USERPWD, $mdsConfig['mds_username'] . ":" . $mdsConfig['mds_password']);
        $checkMetaResponse = curl_exec($checkMeta);
        $result['md_response'] = $checkMetaResponse;
        $result['check_md_status'] = curl_getinfo($checkMeta, CURLINFO_HTTP_CODE);
        curl_close($checkMeta);

        $I->assertEquals(200, $result['check_md_status']);
    }

    public function tryGetTheStatusReturnOfNonExistingDOI (FunctionalTester $I)
    {
        $mdsConfig = $this->getMDSConfig();

        $doi = 999999;
        $result = [];

        $checkDoi = curl_init();
        curl_setopt($checkDoi, CURLOPT_URL, $mdsConfig['mds_doi_url'] . '/' . $mdsConfig['mds_prefix']  . '/' . $doi);
        curl_setopt($checkDoi, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($checkDoi, CURLOPT_USERPWD, $mdsConfig['mds_username'] . ":" . $mdsConfig['mds_password']);
        $checkDoiResponse = curl_exec($checkDoi);
        $result['doi_response'] = $checkDoiResponse;
        $result['check_doi_status'] = curl_getinfo($checkDoi, CURLINFO_HTTP_CODE);
        curl_close($checkDoi);

        $I->assertEquals('DOI not found', $result['doi_response']);
        $I->assertEquals(404, $result['check_doi_status']);

        $checkMeta = curl_init();
        curl_setopt($checkMeta, CURLOPT_URL, $mdsConfig['mds_metadata_url'] . '/' . $mdsConfig['mds_prefix']  . '/' . $doi);
        curl_setopt($checkMeta, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($checkMeta, CURLOPT_USERPWD, $mdsConfig['mds_username'] . ":" . $mdsConfig['mds_password']);
        $checkMetaResponse = curl_exec($checkMeta);
        $result['md_response'] = $checkMetaResponse;
        $result['check_md_status'] = curl_getinfo($checkMeta, CURLINFO_HTTP_CODE);
        curl_close($checkMeta);

        $I->assertEquals('DOI is unknown to MDS', $result['md_response']);
        $I->assertEquals(404, $result['check_md_status']);
    }

    public function tryCreateDoiWhenNonExistMetadata(FunctionalTester $I)
    {
        $mdsConfig = $this->getMDSConfig();
        $doi = 999999;
        $result = [];

        $doi_data = "doi=" .$mdsConfig['mds_prefix'] . "/" . $doi . "\n" . "url=http://gigadb.org/dataset/" . $doi;
        $createDoi = curl_init();
        curl_setopt($createDoi, CURLOPT_URL, $mdsConfig['mds_doi_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi);
        curl_setopt($createDoi, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($createDoi, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($createDoi, CURLOPT_POSTFIELDS, $doi_data);
        curl_setopt($createDoi, CURLOPT_HTTPHEADER, array('Content-Type:text/plain;charset=UTF-8'));
        curl_setopt($createDoi, CURLOPT_USERPWD, $mdsConfig['mds_username'] . ":" . $mdsConfig['mds_password']);
        $curl_response = curl_exec($createDoi);
        $result['create_doi_response'] = $curl_response;
        $result['create_doi_status'] = curl_getinfo($createDoi, CURLINFO_HTTP_CODE);
        curl_close($createDoi);

        $I->assertEquals('Can\'t be blank', $result['create_doi_response']);
        $I->assertEquals(422, $result['create_doi_status']);
    }

    public function tryCreateDoiWithMetadata(FunctionalTester $I)
    {
        $mdsConfig = $this->getMDSConfig();

        $doi = 100006;
        $result = [];

        $data = simplexml_load_file('/var/www/tests/_data/test_100006.xml');

        $createMeta = curl_init();
        curl_setopt($createMeta, CURLOPT_URL, $mdsConfig['mds_metadata_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi);
        curl_setopt($createMeta, CURLOPT_POST, 1);
        curl_setopt($createMeta, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($createMeta, CURLOPT_POSTFIELDS, $data->asXML());
        curl_setopt($createMeta, CURLOPT_HTTPHEADER, array('Content-Type:application/xml;charset=UTF-8'));
        curl_setopt($createMeta, CURLOPT_USERPWD, $mdsConfig['mds_username'] . ":" . $mdsConfig['mds_password']);
        $curl_response = curl_exec($createMeta);
        $result['create_md_response'] = $curl_response;
        $result['create_md_status'] = curl_getinfo($createMeta, CURLINFO_HTTP_CODE);
        curl_close($createMeta) ;

        $I->assertEquals("OK (10.80027/100006)", $result['create_md_response']);
        $I->assertEquals("201", $result['create_md_status']);

        $doi_data = "doi=" . $mdsConfig['mds_prefix'] . "/" . $doi . "\n" . "url=http://gigadb.org/dataset/" . $doi;
        $createDoi = curl_init();
        curl_setopt($createDoi, CURLOPT_URL, $mdsConfig['mds_doi_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi);
        curl_setopt($createDoi, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($createDoi, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($createDoi, CURLOPT_POSTFIELDS, $doi_data);
        curl_setopt($createDoi, CURLOPT_HTTPHEADER, array('Content-Type:text/plain;charset=UTF-8'));
        curl_setopt($createDoi, CURLOPT_USERPWD, $mdsConfig['mds_username'] . ":" . $mdsConfig['mds_password']);
        $curl_response = curl_exec($createDoi);
        $result['create_doi_response'] = $curl_response;
        $result['create_doi_status'] = curl_getinfo($createDoi, CURLINFO_HTTP_CODE);
        curl_close($createDoi) ;

        $I->assertEquals("OK", $result['create_doi_response']);
        $I->assertEquals("201", $result['create_doi_status']);
    }

    public function tryCreateDoiWithInvalidCredentials(FunctionalTester $I)
    {
        $mdsConfig = $this->getMDSConfig();

        $doi = 100006;
        $result = [];

        $data = simplexml_load_file('/var/www/tests/_data/test_100006.xml');

        $createMeta = curl_init();
        curl_setopt($createMeta, CURLOPT_URL, $mdsConfig['mds_metadata_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi);
        curl_setopt($createMeta, CURLOPT_POST, 1);
        curl_setopt($createMeta, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($createMeta, CURLOPT_POSTFIELDS, $data->asXML());
        curl_setopt($createMeta, CURLOPT_HTTPHEADER, array('Content-Type:application/xml;charset=UTF-8'));
        curl_setopt($createMeta, CURLOPT_USERPWD, $mdsConfig['invalid_username'] . ":" . $mdsConfig['invalid_password']);
        $curl_response = curl_exec($createMeta);
        $result['create_md_response'] = $curl_response;
        $result['create_md_status'] = curl_getinfo($createMeta, CURLINFO_HTTP_CODE);
        curl_close($createMeta) ;

        $I->assertEquals("Bad credentials", $result['create_md_response']);
        $I->assertEquals("401", $result['create_md_status']);

        $doi_data = "doi=" . $mdsConfig['mds_prefix'] . "/" . $doi . "\n" . "url=http://gigadb.org/dataset/" . $doi;
        $createDoi = curl_init();
        curl_setopt($createDoi, CURLOPT_URL, $mdsConfig['mds_doi_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi);
        curl_setopt($createDoi, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($createDoi, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($createDoi, CURLOPT_POSTFIELDS, $doi_data);
        curl_setopt($createDoi, CURLOPT_HTTPHEADER, array('Content-Type:text/plain;charset=UTF-8'));
        curl_setopt($createDoi, CURLOPT_USERPWD, $mdsConfig['invalid_username'] . ":" . $mdsConfig['invalid_password']);
        $curl_response = curl_exec($createDoi);
        $result['create_doi_response'] = $curl_response;
        $result['create_doi_status'] = curl_getinfo($createDoi, CURLINFO_HTTP_CODE);
        curl_close($createDoi) ;

        $I->assertEquals("Bad credentials", $result['create_doi_response']);
        $I->assertEquals("401", $result['create_doi_status']);
    }

    public function tryUpdateMetadataWithNotRecognizdeXml(FunctionalTester $I)
    {
        $mdsConfig = $this->getMDSConfig();

        $doi = 100006;
        $result = [];

        $data = new SimpleXMLElement("<resource></resource>");

        $createMeta = curl_init();
        curl_setopt($createMeta, CURLOPT_URL, $mdsConfig['mds_metadata_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi);
        curl_setopt($createMeta, CURLOPT_POST, 1);
        curl_setopt($createMeta, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($createMeta, CURLOPT_POSTFIELDS, $data->asXML());
        curl_setopt($createMeta, CURLOPT_HTTPHEADER, array('Content-Type:application/xml;charset=UTF-8'));
        curl_setopt($createMeta, CURLOPT_USERPWD, $mdsConfig['mds_username'] . ":" . $mdsConfig['mds_password']);
        $curl_response = curl_exec($createMeta);
        $result['create_md_response'] = $curl_response;
        $result['create_md_status'] = curl_getinfo($createMeta, CURLINFO_HTTP_CODE);
        curl_close($createMeta);

        $I->assertEquals("Metadata format not recognized", $result['create_md_response']);
        $I->assertEquals("415", $result['create_md_status']);
    }

    public function tryUpdateMetadataWithInvalidXml(FunctionalTester $I)
    {
        $mdsConfig = $this->getMDSConfig();

        $doi = 100006;
        $result = [];

        $data = simplexml_load_file('/var/www/tests/_data/test_invalid.xml');

        $createMeta = curl_init();
        curl_setopt($createMeta, CURLOPT_URL, $mdsConfig['mds_metadata_url'] . '/' . $mdsConfig['mds_prefix'] . '/' . $doi);
        curl_setopt($createMeta, CURLOPT_POST, 1);
        curl_setopt($createMeta, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($createMeta, CURLOPT_POSTFIELDS, $data->asXML());
        curl_setopt($createMeta, CURLOPT_HTTPHEADER, array('Content-Type:application/xml;charset=UTF-8'));
        curl_setopt($createMeta, CURLOPT_USERPWD, $mdsConfig['mds_username'] . ":" . $mdsConfig['mds_password']);
        $curl_response = curl_exec($createMeta);
        $result['create_md_response'] = $curl_response;
        $result['create_md_status'] = curl_getinfo($createMeta, CURLINFO_HTTP_CODE);
        curl_close($createMeta);

        $I->assertContains("DOI 10.80027/100006: Missing child element(s). Expected is one of ( {http://datacite.org/schema/kernel-4}creators", $result['create_md_response']);
        $I->assertEquals("422", $result['create_md_status']);
    }
}
