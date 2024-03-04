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

    // tests
    public function tryGetTheURLOfAGivenDOI(FunctionalTester $I)
    {
        $config = require("/var/www/protected/config/local.php");

        $mds_doi_url = $config['params']['mds_doi_url'];
        $mds_username = $config['params']['mds_username'];
        $mds_password = $config['params']['mds_password'];
        $mds_prefix = $config['params']['mds_prefix'];


        $doi = 100006;
        $result = [];

        $checkDoi = curl_init();
        curl_setopt($checkDoi, CURLOPT_URL, $mds_doi_url . '/' . $mds_prefix  . '/' . $doi);
        curl_setopt($checkDoi, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($checkDoi, CURLOPT_USERPWD, $mds_username . ":" . $mds_password);
        $checkDoiResponse = curl_exec($checkDoi);
        $result['doi_response'] = $checkDoiResponse;
        $result['check_doi_status'] = curl_getinfo($checkDoi, CURLINFO_HTTP_CODE);
        curl_close($checkDoi);

        $I->assertEquals('http://gigadb.org/dataset/100006', $result['doi_response']);
        $I->assertEquals(200, $result['check_doi_status']);
    }

    public function tryGetTheMetadataOfAGivenDOI(FunctionalTester $I)
    {
        $config = require("/var/www/protected/config/local.php");

        $mds_metadata_url = $config['params']['mds_metadata_url'];
        $mds_username = $config['params']['mds_username'];
        $mds_password = $config['params']['mds_password'];
        $mds_prefix = $config['params']['mds_prefix'];


        $doi = 100006;
        $result = [];

        $checkMeta = curl_init();
        curl_setopt($checkMeta, CURLOPT_URL, $mds_metadata_url . '/' . $mds_prefix  . '/' . $doi);
        curl_setopt($checkMeta, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($checkMeta, CURLOPT_USERPWD, $mds_username . ":" . $mds_password);
        $checkMetaResponse = curl_exec($checkMeta);
        $result['md_response'] = $checkMetaResponse;
        $result['check_md_status'] = curl_getinfo($checkMeta, CURLINFO_HTTP_CODE);
        curl_close($checkMeta);

        $I->assertContains('<identifier identifierType="DOI">10.80027/100006</identifier>', $result['md_response']);
        $I->assertEquals(200, $result['check_md_status']);
    }
}
