<?php

/**
 * Unit tests for StoredDatasetAccessions to retrieve dataset accessions from the database
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetAccessionsTest extends CDbTestCase
{
    protected $fixtures = array(
        'datasets' => 'Dataset',
        'links' => 'Link',
        'prefixes' => 'Prefix',
    );

    public function setUp()
    {
        parent::setUp();
    }

    public function testStoredReturnsDatasetDOI()
    {
        $dataset_id = 1;
        $doi = 100243;
        $daoUnderTest = new StoredDatasetAccessions($dataset_id, $this->getFixtureManager()->getDbConnection());
        $this->assertEquals($doi, $daoUnderTest->getDatasetDOI()) ;
    }

    /**
     * test that this DAO class return a Dataset's primary links from storage
     *
     */
    public function testStoredReturnsPrimaryLinks()
    {

        $dataset_id = 1;

        $dao_under_test = new StoredDatasetAccessions($dataset_id, $this->getFixtureManager()->getDbConnection());
        $primaryLinks = $dao_under_test->getPrimaryLinks();
        $nb_primary_links = count($primaryLinks);
        $this->assertEquals(2, $nb_primary_links);
        $counter = 0;
        while ($counter < $nb_primary_links) {
            $this->assertEquals($this->links($counter)->is_primary, $primaryLinks[$counter]->is_primary);
            $this->assertEquals($this->links($counter)->link, $primaryLinks[$counter]->link);
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

        $dao_under_test = new StoredDatasetAccessions($dataset_id, $this->getFixtureManager()->getDbConnection());
        $secondaryLinks = $dao_under_test->getSecondaryLinks();
        $nb_secondaryLinks = count($secondaryLinks);
        $this->assertEquals(3, $nb_secondaryLinks);
        $counter = 0;
        while ($counter < $nb_secondaryLinks) {
            $this->assertEquals($this->links($counter + 2)->is_primary, $secondaryLinks[$counter]->is_primary);
            $this->assertEquals($this->links($counter + 2)->link, $secondaryLinks[$counter]->link);
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

        $dao_under_test = new StoredDatasetAccessions($doi, $this->getFixtureManager()->getDbConnection());
        $prefixes = $dao_under_test->getPrefixes();
        $nb_prefixes = count($prefixes);
        $this->assertEquals(2, $nb_prefixes);
        $counter = 0;
        while ($counter < $nb_prefixes) {
            $this->assertEquals($this->prefixes($counter)->prefix, $prefixes[$counter]['prefix']);
            $this->assertEquals($this->prefixes($counter)->url, $prefixes[$counter]['url']);
            $this->assertEquals($this->prefixes($counter)->source, $prefixes[$counter]['source']);
            $counter++;
        }
    }
}
