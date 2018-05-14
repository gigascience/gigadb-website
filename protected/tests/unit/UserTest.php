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
}