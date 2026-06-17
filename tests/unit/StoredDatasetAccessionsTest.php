<?php

declare(strict_types=1);

require_once __DIR__ . '/LoadingFixtureTrait.php';
require_once __DIR__ . '/CdbUnit.php';

use Codeception\Test\Unit;

/**
 * Unit tests for StoredDatasetAccessions to retrieve dataset accessions from the database
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetAccessionsTest extends CdbUnit
{
    use LoadingFixtureTrait;

    public function _before()
    {
        $db = $this->getModule('Db')->_getDbh();
        $db->exec('TRUNCATE TABLE gigadb_user CASCADE');
        $db->exec('TRUNCATE TABLE dataset CASCADE');
        $db->exec('TRUNCATE TABLE link CASCADE');
        $db->exec('TRUNCATE TABLE prefix CASCADE');

        $this->loadFixture('gigadb_user', \User::class);
        $this->loadFixture('dataset', \Dataset::class);
        $this->loadFixture('link', \Link::class);
        $this->loadFixture('prefix', \Prefix::class);

        parent::_before();
    }

    public function testStoredReturnsDatasetDOI()
    {
        $dataset_id = 1;
        $doi = 100243;
        $daoUnderTest = new StoredDatasetAccessions($dataset_id, $this->cdbConnection);
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI()) ;
    }

    /**
     * test that this DAO class return a Dataset's primary links from storage
     *
     */
    public function testStoredReturnsPrimaryLinks()
    {
        $dataset_id = 1;

        $dao_under_test = new StoredDatasetAccessions($dataset_id, $this->cdbConnection);
        $primaryLinks = $dao_under_test->getPrimaryLinks();
        $nb_primary_links = count($primaryLinks);
        $this->assertEquals(2, $nb_primary_links);
        $counter = 0;
        while ($counter < $nb_primary_links) {
            $link = Link::model()->findByPk($counter + 1);
            $this->assertEquals($link->is_primary, $primaryLinks[$counter]->is_primary);
            $this->assertEquals($link->link, $primaryLinks[$counter]->link);
            $counter++;
        }
    }

    /**
     * test that this DAO class return a Dataset's secondary links from storage
     *
     */
    public function testStoredReturnsSecondaryLinks()
    {
        $dataset_id = 1;

        $dao_under_test = new StoredDatasetAccessions($dataset_id, $this->cdbConnection);
        $secondaryLinks = $dao_under_test->getSecondaryLinks();
        $nb_secondaryLinks = count($secondaryLinks);
        $this->assertEquals(3, $nb_secondaryLinks);
        $counter = 0;
        while ($counter < $nb_secondaryLinks) {
            $link = Link::model()->findByPk($counter + 3);
            $this->assertEquals($link->is_primary, $secondaryLinks[$counter]->is_primary);
            $this->assertEquals($link->link, $secondaryLinks[$counter]->link);
            $counter++;
        }
    }

    /**
     * test that this DAO class return all prefixes from storage
     *
     */
    public function testStoredReturnsPrefixes()
    {
        $doi = 100243;

        $dao_under_test = new StoredDatasetAccessions($doi, $this->cdbConnection);
        $prefixes = $dao_under_test->getPrefixes();
        $nb_prefixes = count($prefixes);
        $this->assertEquals(2, $nb_prefixes);
        $counter = 0;
        while ($counter < $nb_prefixes) {
            $prefix = Prefix::model()->findByPk($counter + 1);
            $this->assertEquals($prefix->prefix, $prefixes[$counter]['prefix']);
            $this->assertEquals($prefix->url, $prefixes[$counter]['url']);
            $this->assertEquals($prefix->source, $prefixes[$counter]['source']);
            $counter++;
        }
    }
}
