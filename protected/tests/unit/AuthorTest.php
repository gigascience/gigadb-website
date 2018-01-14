<?php


class AuthorTest extends CDbTestCase
{
    protected $fixtures=array(
        'authors'=>'Author',
    );
 

 	function testDisplayNameSubstring() {
 		 $this->assertEquals("Muñoz, Á, G", $this->authors(0)->getDisplayName(),"First names with accentuated character display correctly (#82)");
 		 $this->assertEquals("Montana, C, Á", $this->authors(1)->getDisplayName(),"Middle names with accentuated character display correctly (#82)");
 	}

}

?>