<?php


class AuthorTest extends CDbTestCase
{
    protected $fixtures=array(
        'authors'=>'Author',
    );
 

 	function testDisplayNameSubstring() {
 		$expectations = array("Muñoz, Á, G", "Montana, C, Á", "Martinez-Cruzado, J, C", "Potato Genome Sequencing Consortium", "Gilbert, MP") ;
 		foreach ($expectations as $indice => $expectation) {
	 		$this->assertEquals($expectation, $this->authors($indice)->getDisplayName(),"First names with accentuated character display correctly (#82)");
 		}
 	}

 	function testSurname() {
 		$expectations = array ("Muñoz", "Montana", "Martinez-Cruzado","Potato Genome Sequencing Consortium", "Gilbert") ;
 		foreach ($expectations as $indice => $expectation) {
			$this->assertEquals($expectation, $this->authors($indice)->getSurname(),"Surname is returned");
 		}
 	}

 	 	function testInitials() {
 		$expectations = array("ÁGG", "CÁ", "JC", "", "MTP") ;
 		foreach ($expectations as $indice => $expectation) {
	 		$this->assertEquals($expectation, $this->authors($indice)->getInitials(),"Initials is returned in correct format");
 		}
 	}

}

?>