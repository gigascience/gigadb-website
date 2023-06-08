<?php

class DatasetTest extends CDbTestCase
{
    protected $fixtures = array(
        'datasets' => 'Dataset',
        'authors' => 'Author',
        'dataset_author' => 'DatasetAuthor',
    );

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
}
