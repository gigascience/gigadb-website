<?php
namespace GigaDB\Tests\UnitTests;


/**
 * unit tests for user class
 */
class UserDAOTest extends \CDbTestCase
{
	public function testFindByEmail()
	{
		$sut = new \UserDAO();// System Under Test
		$user = $sut->findByEmail("user@gigadb.org");
		$this->assertEquals($user->getFullName(), "John Smith");
	}

}
?>