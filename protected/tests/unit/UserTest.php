<?php


class UserTest extends CDbTestCase
{
	protected $fixtures=array(
        'authors'=>'Author',
    );

	function testReturnsLinkedAuthor() {
		$user = User::model()->findByPk(345);
		$this->assertEquals($this->authors(2), $user->getLinkedAuthor(),"Retrieve A3 linked to default user");
	}

	function testReturnsFullName() {
		$user = User::model()->findByPk(345);
		$this->assertEquals("John Smith", $user->getFullName(),"Retrieve full name of a user");
	}
}