<?php


class AuthorTest extends CDbTestCase
{
    protected $fixtures=array(
        'authors'=>'Author',
    );
 

 	function testDisplayName() {
 		 $this->assertEquals("Muñoz, Á, G", $this->authors(0)->getDisplayName());
 	}

}

?>