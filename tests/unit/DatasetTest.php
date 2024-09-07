<?php

/**
 * Test non getter/setter methods from the Dataset model class
 *
 * How to run:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run --debug unit DatasetTest
 *
 */
class DatasetTest extends CDbTestCase
{
    protected $fixtures = array(
        'datasets' => 'Dataset',
        'authors' => 'Author',
        'dataset_author' => 'DatasetAuthor',
    );

    public function testUploadStatusValidation()
    {
        $myDataset = $this->datasets(0);

        $this->assertTrue($myDataset->validate());
        $this->assertContains($myDataset->upload_status, array_merge(Dataset::ORIGINAL_UPLOAD_STATUS_LIST, Dataset::FUW_UPLOAD_STATUS_LIST));

        $myDataset->upload_status = 'invalid';

        $this->assertFalse($myDataset->validate());
    }

    function testGetAuthors()
    {
        $this->assertGreaterThan(0, count($this->datasets(0)->authors), "dataset returns its two authors");
    }

    function testGetAuthorNames()
    {
        $authorNames = '<a class="result-sub-links" href="/search/new?keyword=Montana CÁG&amp;author_id=2">Montana CÁG</a>; <a class="result-sub-links" href="/search/new?keyword=Muñoz ÁGG&amp;author_id=1">Muñoz ÁGG</a>; <a class="result-sub-links" href="/search/new?keyword=Schiøtt M&amp;author_id=7">Schiøtt M</a>';


        $this->assertEquals($authorNames, $this->datasets(0)->authorNames, "dataset returns formatted authors name");
    }

    function testGetCuratorName()
    {
        $this->assertEquals("", $this->datasets(0)->getCuratorName(), "No curator, so empty string returned on getCuratorName()");
        $this->assertEquals("Joe Bloggs", $this->datasets(1)->getCuratorName(), "Full name returned on getCuratorName()");
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
