<?php
 /**
 * Functional test for the AdminFileTest form
 *
 * It just tests the that sizes display correctly
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class MailServiceTest extends FunctionalTesting
{

	public function testItShouldSendMessage() {
		try {
			$srv = new MailService();
			$result = $srv->sendEmailMessage("foo@bar.com", "hello@world.com", "Testing", "lorem ipsum");
			$this->assertTrue($result);
		}
		catch(Error $e) {
			$this->fail("Exception thrown: ".$e->getMessage());
		}
	}
}