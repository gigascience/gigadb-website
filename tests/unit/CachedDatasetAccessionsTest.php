<?php

declare(strict_types=1);

require_once __DIR__ . '/LoadingFixtureTrait.php';

use Codeception\Test\Unit;
/**
 * Unit tests for CachedDatasetAccessions to retrieve dataset accessions from a cache
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetAccessionsTest extends Unit
{
    use LoadingFixtureTrait;

    public function _before()
    {
        $db = $this->getModule('Db')->_getDbh();
        $db->exec('TRUNCATE TABLE dataset CASCADE');
        $db->exec('TRUNCATE TABLE link CASCADE');
        $db->exec('TRUNCATE TABLE gigadb_user CASCADE');

        $this->loadFixture('gigadb_user', \User::class);
        $this->loadFixture('dataset', \Dataset::class);
        $this->loadFixture('link', \Link::class);
    }
    /**
     * test that this DAO class return a Dataset's Primary links from cache
     *
     */
    public function testCachedReturnsPrimaryLinksCacheHit()
    {

        $dataset_id = 1;
        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get'])
                         ->getMock();
        //then we set our expectation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetAccessions_getPrimaryLinks"))
                 ->willReturn([Link::model()->findByPk(1), Link::model()->findByPk(2)]);

        // create a mock for the StoredDatasetAccessions
        $storedDatasetAccessions = $this->getMockBuilder(StoredDatasetAccessions::class)
                         ->setMethods(['getDatasetId'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Hit
        $storedDatasetAccessions->expects($this->once())
                 ->method('getDatasetId')
                 ->willReturn(1);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $dao_under_test = new CachedDatasetAccessions(
            $cache,
            $cacheDependency,
            $storedDatasetAccessions
        );
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
     * test that this DAO class return a Dataset's Primary links from cache but cache expired or was invalidated
     * In this case the information should be fetched from storage
     */
    public function testCachedReturnsPrimaryLinksCacheMiss()
    {
        $dataset_id = 1;

        $expected = array(
            array(
                'id' => 1,
                'dataset_id' => 1,
                'is_primary' => true,
                'link' => 'ENA:PRJEB225',
                'description' => '',
            ),
            array(
                'id' => 2,
                'dataset_id' => 1,
                'is_primary' => true,
                'link' => 'http://www.ncbi.nlm.nih.gov/projects/SNP/snp_viewBatch.cgi?sbid=1056308',
                'description' => '',
            ),
        );
        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get','set'])
                         ->getMock();
        //then we set our expectation for a Cache Miss
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetAccessions_getPrimaryLinks"))
                 ->willReturn(false);

        // create a mock for the StoredDatasetAccessions
        $storedDatasetAccessions = $this->getMockBuilder(StoredDatasetAccessions::class)
                         ->setMethods(['getDatasetId','getPrimaryLinks'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Miss
        $storedDatasetAccessions->expects($this->exactly(2))
                 ->method('getDatasetId')
                 ->willReturn(1);

        $storedDatasetAccessions->expects($this->exactly(1))
         ->method('getPrimaryLinks')
         ->willReturn([Link::model()->findByPk(1), Link::model()->findByPk(2)]);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        //when there is a cache miss, we also expect the value to be set into the cache for 24 hours
        $cache->expects($this->once())
                 ->method('set')
                 ->with(
                     $this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetAccessions_getPrimaryLinks"),
                     [Link::model()->findByPk(1), Link::model()->findByPk(2)],
                     Cacheable::defaultTTL * 30,
                     $cacheDependency
                 )
                ->willReturn(true);

        $dao_under_test = new CachedDatasetAccessions(
            $cache,
            $cacheDependency,
            $storedDatasetAccessions
        );
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
     * test that this DAO class return a Dataset's Secondary links from cache
     *
     */
    public function testCachedReturnsSecondaryLinksCacheHit()
    {

        $dataset_id = 1;
        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get'])
                         ->getMock();
        //then we set our expectation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetAccessions_getSecondaryLinks"))
                 ->willReturn([Link::model()->findByPk(3), Link::model()->findByPk(4), Link::model()->findByPk(5)]);

         // create a mock for the StoredDatasetAccessions
        $storedDatasetAccessions = $this->getMockBuilder(StoredDatasetAccessions::class)
                         ->setMethods(['getDatasetId'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Hit
        $storedDatasetAccessions->expects($this->once())
                 ->method('getDatasetId')
                 ->willReturn(1);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $dao_under_test = new CachedDatasetAccessions(
            $cache,
            $cacheDependency,
            $storedDatasetAccessions
        );
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
     * test that this DAO class return a Dataset's Secondary links from cache but cache expired or was invalidated
     * In this case the information should be fetched from storage
     *
     */
    public function testCachedReturnsSecondaryLinksCacheMiss()
    {

        $dataset_id = 1;
        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get', 'set'])
                         ->getMock();
        //then we set our expectation for a Cache Miss
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetAccessions_getSecondaryLinks"))
                 ->willReturn(false);

        // create a mock for the StoredDatasetAccessions
        $storedDatasetAccessions = $this->getMockBuilder(StoredDatasetAccessions::class)
                         ->setMethods(['getDatasetId','getSecondaryLinks'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Miss
        $storedDatasetAccessions->expects($this->exactly(2))
                 ->method('getDatasetId')
                 ->willReturn(1);

        $storedDatasetAccessions->expects($this->exactly(1))
         ->method('getSecondaryLinks')
         ->willReturn([Link::model()->findByPk(3), Link::model()->findByPk(4), Link::model()->findByPk(5)]);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        //when there is a cache miss, we also expect the value to be set into the cache for 24 hours
        $cache->expects($this->once())
                 ->method('set')
                 ->with(
                     $this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetAccessions_getSecondaryLinks"),
                     [Link::model()->findByPk(3), Link::model()->findByPk(4), Link::model()->findByPk(5)],
                     Cacheable::defaultTTL * 30,
                     $cacheDependency
                 )
                ->willReturn(true);


        $dao_under_test = new CachedDatasetAccessions(
            $cache,
            $cacheDependency,
            $storedDatasetAccessions
        );
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
     * test that this DAO class return all prefixes from cache
     *
     */
    public function testCachedReturnsPrefixesCacheHit()
    {
        $dataset_id = 1;

        $expected = array(
            array(
                'id' => 1,
                'prefix' => 'ENA',
                'url' => 'http://www.ebi.ac.uk/ena/data/view/',
                'source' => 'EBI',
                'icon' => '',
            ),
            array(
                'id' => 2,
                'prefix' => 'SRA',
                'url' => 'http://www.ncbi.nlm.nih.gov/sra?term=',
                'source' => 'NCBI',
                'icon' => '',
            ),
        );

        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get'])
                         ->getMock();
        //then we set our expectation for a Cache Hit
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetAccessions_getPrefixes"))
                 ->willReturn($expected);

         // create a mock for the StoredDatasetAccessions
        $storedDatasetAccessions = $this->getMockBuilder(StoredDatasetAccessions::class)
                         ->setMethods(['getDatasetId'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Hit
        $storedDatasetAccessions->expects($this->once())
                 ->method('getDatasetId')
                 ->willReturn(1);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

        $dao_under_test = new CachedDatasetAccessions(
            $cache,
            $cacheDependency,
            $storedDatasetAccessions
        );

        $this->assertEquals($expected, $dao_under_test->getPrefixes());
    }

    /**
     * test that this DAO class return all prefixes from cache but with cache miss
     *
     */
    public function testCachedReturnsPrefixesCacheMiss()
    {
        $dataset_id = 1;

        $expected = array(
            array(
                'id' => 1,
                'prefix' => 'ENA',
                'url' => 'http://www.ebi.ac.uk/ena/data/view/',
                'source' => 'EBI',
                'icon' => '',
            ),
            array(
                'id' => 2,
                'prefix' => 'SRA',
                'url' => 'http://www.ncbi.nlm.nih.gov/sra?term=',
                'source' => 'NCBI',
                'icon' => '',
            ),
        );
        //we first need to create a mock object for the cache
        $cache = $this->getMockBuilder(CApcCache::class)
                         ->setMethods(['get','set'])
                         ->getMock();
        //then we set our expectation for a Cache Miss
        $cache->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetAccessions_getPrefixes"))
                 ->willReturn(false);

        // create a mock for the StoredDatasetAccessions
        $storedDatasetAccessions = $this->getMockBuilder(StoredDatasetAccessions::class)
                         ->setMethods(['getDatasetId','getPrefixes'])
                         ->disableOriginalConstructor()
                         ->getMock();
        //then we set our expectation for a Cache Miss
        $storedDatasetAccessions->expects($this->exactly(2))
                 ->method('getDatasetId')
                 ->willReturn(1);

        $storedDatasetAccessions->expects($this->exactly(1))
         ->method('getPrefixes')
         ->willReturn($expected);

        // create a stub of the cache dependency (because we don't need to verify expectations on the cache dependency)
        $cacheDependency = $this->createMock(CCacheDependency::class);

         //when there is a cache miss, we also expect the value to be set into the cache for 24 hours
        $cache->expects($this->once())
                 ->method('set')
                 ->with(
                     $this->equalTo("dataset_${dataset_id}_ALL_0_CachedDatasetAccessions_getPrefixes"),
                     $expected,
                     Cacheable::defaultTTL * 30,
                     $cacheDependency
                 )
                ->willReturn(true);

        $dao_under_test = new CachedDatasetAccessions(
            $cache,
            $cacheDependency,
            $storedDatasetAccessions
        );

        $this->assertEquals($expected, $dao_under_test->getPrefixes());
    }
}
