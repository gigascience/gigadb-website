<?php

declare(strict_types=1);

use Codeception\Test\Unit;
/**
 * unit tests for user class
 */
class UserDAOTest extends Unit
{
    public function testFindByEmail()
    {
        $sut = new \UserDAO();// System Under Test
        $user = $sut->findByEmail("user@gigadb.org");
        $this->assertEquals($user->getFullName(), "John Smith");
    }
}
