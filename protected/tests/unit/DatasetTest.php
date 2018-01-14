<?php


class DatasetTest extends CDbTestCase
{
    protected $fixtures=array(
        'datasets'=>'Dataset',
        'authors'=>'Author',
    );
 
 	function testGetAuthors() {
 		$this->assertGreaterThan(0, count($this->datasets(0)->authors),"dataset returns its two authors");
 	}

 	function testGetAuthorNames() {
 		$authorNames = '<a class="result-sub-links" href="/search/new?keyword=Montana, C, Á&amp;author_id=2">Montana, C, Á</a>; <a class="result-sub-links" href="/search/new?keyword=Muñoz, Á, G&amp;author_id=1">Muñoz, Á, G</a>';
 		

 		$this->assertEquals($authorNames, $this->datasets(0)->authorNames, "dataset returns formatted authors name");
 	}

 }

 ?>