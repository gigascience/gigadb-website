<?php


class DatasetTest extends CDbTestCase
{
    protected $fixtures=array(
        'datasets'=>'Dataset',
        'authors'=>'Author',
        'dataset_author'=>'DatasetAuthor',
    );

 	function testGetAuthors() {
 		$this->assertGreaterThan(0, count($this->datasets(0)->authors),"dataset returns its two authors");
 	}

 	function testGetAuthorNames() {
 		$authorNames = '<a class="result-sub-links" href="/search/new?keyword=Montana CÁG&amp;author_id=2">Montana CÁG</a>; <a class="result-sub-links" href="/search/new?keyword=Muñoz ÁGG&amp;author_id=1">Muñoz ÁGG</a>; <a class="result-sub-links" href="/search/new?keyword=Schiøtt M&amp;author_id=7">Schiøtt M</a>';


 		$this->assertEquals($authorNames, $this->datasets(0)->authorNames, "dataset returns formatted authors name");
 	}

 	function testGetCuratorName() {
 		$this->assertEquals("", $this->datasets(0)->getCuratorName(),"No curator, so empty string returned on getCuratorName()");
 		$this->assertEquals("Joe Bloggs", $this->datasets(1)->getCuratorName(),"Full name returned on getCuratorName()");
 	}

//    function testSetIdentifier() {
//        $lastDataset = Dataset::model()->find(array('order'=>'identifier desc'));
//        $lastIdentifier = intval($lastDataset->identifier);
//
//        $dataset = new Dataset();
//        $dataset->setIdentifier();
//
//        $this->assertEquals($lastIdentifier + 1, $dataset->identifier);
//    }
//
//    function testLoadByData() {
//        $data = array(
//            'manuscript_id' => 'test manuscript_id',
//            'title' => 'test title',
//            'description' => 'test description',
//        );
//
//        $dataset = new Dataset();
//        $dataset->loadByData($data);
//
//        $this->assertEquals('test manuscript_id', $dataset->manuscript_id);
//        $this->assertEquals('test title', $dataset->title);
//        $this->assertEquals('test description', $dataset->description);
//    }
//
//    function testSave() {
//        $data = array(
//            'submitter_id' => 345,
//            'manuscript_id' => 'test manuscript_id',
//            'title' => 'test title',
//            'description' => 'test description',
//            'upload_status' => 'Incomplete',
//            'ftp_site' => "''",
//        );
//
//        $dataset = new Dataset();
//        $dataset->loadByData($data);
//        $res = $dataset->save();
//
//        $this->assertTrue($res);
//
//        $newKeywords = array(
//            'test keyword1',
//            'test keyword2',
//            'test keyword3',
//        );
//        $dataset->updateKeywords($newKeywords);
//        $this->assertEquals($newKeywords, $dataset->getSemanticKeywords());
//
//        $res = $dataset->delete();
//        $this->assertTrue($res);
//    }
//
//    function testUpdateKeywords() {
//        $dataset = $this->datasets(0);
//
//        $newKeywords = array(
//            'test keyword1',
//            'test keyword2',
//            'test keyword3',
//        );
//        $dataset->updateKeywords($newKeywords);
//        $this->assertEquals($newKeywords, $dataset->getSemanticKeywords());
//    }
//
//    function testUpdateTypes() {
//        $dataset = $this->datasets(0);
//
//        $newTypes = array(
//            2,
//            4,
//        );
//        $dataset->updateKeywords($newTypes);
//        $this->assertEquals($newTypes, $dataset->getTypeIds());
//    }
//
//    function testAddAuthor() {
//        $dataset = $this->datasets(1);
//        $author = $this->authors(2);
//
//        $dataset->addAuthor($author);
//        $authors = $dataset->getAuthor();
//        $this->assertEquals(1, count($authors));
//        $this->assertEquals('Juan', $authors[0]['first_name']);
//    }
 }
