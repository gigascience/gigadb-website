<?php


class AuthorTest extends CDbTestCase
{
    protected $fixtures=array(
        'authors'=>'Author',
    );



 	function testSurname() {
 		$expectations = array ("Muñoz",
 								 "Montana",
 								 "Martinez-Cruzado",
 								 "Potato Genome Sequencing Consortium",
 								 "Gilbert",
 								 "Régime",
 								 "Schiøtt") ;
 		foreach ($expectations as $indice => $expectation) {
			$this->assertEquals($expectation, $this->authors($indice)->getSurname(),"Surname is returned");
 		}
 	}

 	function testInitials() {
 		$expectations = array("ÁGG", "CÁG", "Jc", "", "MTP", "JÉ", "M") ;
 		foreach ($expectations as $indice => $expectation) {
	 		$this->assertEquals($expectation, $this->authors($indice)->getInitials(),"Initials is returned in correct format");
 		}
 	}


 	function testDisplayNameSubstring() {
 		$expectations = array("Muñoz ÁGG",
 							 "Montana CÁG",
 							 "Martinez-Cruzado Jc", //sometimes a name is meant to stay lowercase even in initial form
 							 "Potato Genome Sequencing Consortium",
 							 "Gilbert MTP",
 							 "Régime JÉ",
 							 "Schiøtt M",
 							 "T.E Lawrence") ;
 		foreach ($expectations as $indice => $expectation) {
	 		$this->assertEquals($expectation, $this->authors($indice)->getDisplayName(),"First names with accentuated character display correctly (#82), calculated and custom names too");
 		}
 	}

 	function testFindAttachedAuthorByUserIdWhenAttached() {
 		$expectation = "Martinez-Cruzado Jc";
 		$this->assertEquals($expectation, Author::findAttachedAuthorByUserId(345)->getDisplayName(),
 							"return author attached to user");
 	}

 	function testFindAttachedAuthorByUserIdWhenNotAttached() {
 		$expectation = null;
 		$this->assertEquals(null, Author::findAttachedAuthorByUserId(344),
 							"return no author attached to user");
 	}



}

?>