<?php

declare(strict_types=1);

require_once __DIR__ . '/LoadingFixtureTrait.php';
require_once __DIR__ . '/CdbUnit.php';

use Codeception\Test\Unit;

/**
 * Unit tests for StoredDatasetExternalLinks to retrieve from storage, external links for associated dataset
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetExternalLinksTest extends CdbUnit
{
    use LoadingFixtureTrait;

    public function _before()
    {
        $db = $this->getModule('Db')->_getDbh();
        $db->exec('TRUNCATE TABLE external_link_type CASCADE');
        $db->exec('TRUNCATE TABLE dataset CASCADE');
        $db->exec('TRUNCATE TABLE gigadb_user CASCADE');
        $db->exec('TRUNCATE TABLE external_link CASCADE');

        $this->loadFixture('external_link_type', \ExternalLinkType::class);
        $this->loadFixture('gigadb_user', \User::class);
        $this->loadFixture('dataset', \Dataset::class);
        $this->loadFixture('external_link', \ExternalLink::class);

        parent::_before();
    }

    public function testStoredReturnsDatasetId()
    {
        $dataset_id = 1;

        $daoUnderTest = new StoredDatasetExternalLinks(
            $dataset_id,
            $this->cdbConnection
        );
        $this->assertEquals($dataset_id, $daoUnderTest->getDatasetId()) ;
    }

    public function testStoredReturnsDatasetDOI()
    {
        $dataset_id = 1;
        $doi = 100243;
        $daoUnderTest = new StoredDatasetExternalLinks(
            $dataset_id,
            $this->cdbConnection
        );
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI()) ;
    }

    public function testStoredReturnsExternalLinks()
    {
        $dataset_id = 1;

        $expected = array(
            array(
                'id' => 1,
                'dataset_id' => 1,
                'url' => "http://foo.com",
                'external_link_type_id' => 1,
                'external_link_type_name' => "Additional information",
            ),
            array(
                'id' => 2,
                'dataset_id' => 1,
                'url' => "http://foo2.com",
                'external_link_type_id' => 1,
                'external_link_type_name' => "Additional information",
            ),
            array(
                'id' => 3,
                'dataset_id' => 1,
                'url' => "http://foo3.com",
                'external_link_type_id' => 2,
                'external_link_type_name' => "Genome browser",
            ),
            array(
                'id' => 4,
                'dataset_id' => 1,
                'url' => "http://foo4.com",
                'external_link_type_id' => 3,
                'external_link_type_name' => "Protocols.io",
            ),
            array(
                'id' => 5,
                'dataset_id' => 1,
                'url' => "http://foo5.com",
                'external_link_type_id' => 4,
                'external_link_type_name' => "JBrowse",
            ),
        );

        $daoUnderTest = new StoredDatasetExternalLinks(
            $dataset_id,
            $this->cdbConnection
        );
        $this->assertEquals($expected, $daoUnderTest->getDatasetExternalLinks()) ;
        $this->assertEquals([$expected[2]], $daoUnderTest->getDatasetExternalLinks(["Genome browser"])) ;
        $this->assertEquals([$expected[3], $expected[4]], $daoUnderTest->getDatasetExternalLinks(["Protocols.io","JBrowse"])) ;
        $this->assertEquals([], $daoUnderTest->getDatasetExternalLinks(["fake"])) ;
    }

    public function testStoredReturnsExternalLinksTypesAndCount()
    {
        $dataset_id = 1;

        $expected = array(
            "Additional information" => 2,
            "Genome browser" => 1,
            "Protocols.io" => 1,
            "JBrowse" => 1,
        );

        $daoUnderTest = new StoredDatasetExternalLinks(
            $dataset_id,
            $this->cdbConnection
        );
        $this->assertEquals($expected, $daoUnderTest->getDatasetExternalLinksTypesAndCount()) ;

        $expected2 = array(
            "Additional information" => 2,
        );
        $this->assertEquals($expected2, $daoUnderTest->getDatasetExternalLinksTypesAndCount(["Additional information"])) ;

        $dataset_id = 3;

        $daoUnderTestEmpty = new StoredDatasetExternalLinks(
            $dataset_id,
            $this->cdbConnection
        );
        $this->assertEquals([], $daoUnderTestEmpty->getDatasetExternalLinksTypesAndCount()) ;
        $this->assertEquals([], $daoUnderTestEmpty->getDatasetExternalLinksTypesAndCount(["Additional information"])) ;
    }
}
