<?php

declare(strict_types=1);

require_once __DIR__ . '/LoadingFixtureTrait.php';

use Codeception\Test\Unit;

/**
 * Test non getter/setter methods from the Dataset model class
 *
 * How to run:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run --debug unit DatasetTest
 *
 */
class DatasetTest extends Unit
{
    use LoadingFixtureTrait;

    public function _before()
    {
        $db = $this->getModule('Db')->_getDbh();
        $db->exec('TRUNCATE TABLE gigadb_user CASCADE');
        $db->exec('TRUNCATE TABLE dataset CASCADE');
        $db->exec('TRUNCATE TABLE author CASCADE');
        $db->exec('TRUNCATE TABLE dataset_author CASCADE');

        $this->loadFixture('gigadb_user', \User::class);
        $this->loadFixture('dataset', \Dataset::class);
        $this->loadFixture('author', \Author::class);
        $this->loadFixture('dataset_author', \DatasetAuthor::class);
    }

    public function testUploadStatusValidation()
    {
        $myDataset = Dataset::model()->findByPk(1);

        $this->assertTrue($myDataset->validate());
        $this->assertContains($myDataset->upload_status, array_merge(Dataset::ORIGINAL_UPLOAD_STATUS_LIST, Dataset::FUW_UPLOAD_STATUS_LIST));

        $myDataset->upload_status = 'invalid';

        $this->assertFalse($myDataset->validate());
    }

    function testGetAuthors()
    {
        $this->assertGreaterThan(0, count(Dataset::model()->findByPk(1)->authors), "dataset returns its two authors");
    }

    function testGetAuthorNames()
    {
        $authorNames = '<a class="result-sub-links" href="/search/new?keyword=Schiøtt M&amp;author_id=7">Schiøtt M</a>; <a class="result-sub-links" href="/search/new?keyword=Montana CÁG&amp;author_id=2">Montana CÁG</a>; <a class="result-sub-links" href="/search/new?keyword=Muñoz ÁGG&amp;author_id=1">Muñoz ÁGG</a>';


        $this->assertEquals($authorNames, Dataset::model()->findByPk(1)->authorNames, "dataset returns formatted authors name");
    }

    function testGetCuratorName()
    {
        $this->assertEquals("", Dataset::model()->findByPk(1)->getCuratorName(), "No curator, so empty string returned on getCuratorName()");
        $this->assertEquals("Joe Bloggs", Dataset::model()->findByPk(2)->getCuratorName(), "Full name returned on getCuratorName()");
    }

    function testGetAvailableStatusList()
    {
        $result = Dataset::getAvailableStatusList();
        if (Yii::app()->featureFlag->isEnabled("fuw")) {
            codecept_debug("*** FUW is enabled ***");
            $this->assertCount(count(Dataset::ORIGINAL_UPLOAD_STATUS_LIST)+count(Dataset::FUW_UPLOAD_STATUS_LIST), $result);
            $this->assertTrue(array_diff(Dataset::ORIGINAL_UPLOAD_STATUS_LIST, $result) === []);
            $this->assertTrue(array_diff(Dataset::FUW_UPLOAD_STATUS_LIST, $result) === []);
        }
        else {
            codecept_debug("*** FUW is NOT enabled ***");
            $this->assertCount(count(Dataset::ORIGINAL_UPLOAD_STATUS_LIST), $result);
            $this->assertTrue(array_diff(Dataset::ORIGINAL_UPLOAD_STATUS_LIST, $result) === []);
        }
    }
}
