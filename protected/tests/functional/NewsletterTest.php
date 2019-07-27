<?php

 /**
 * Functional test for the NewsletterService class using Mailchimp API
 *
 * It just tests the ability to subscribe/unsubscribe from the User form
 * and that is it is a properly configured Yii application component
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class NewsletterTest extends FunctionalTesting
{
  	/**
  	 * Test that we can instantiate the service and subscribe an email address
  	 *
  	 * The test email address need to be different each time otherwise it will be banned by Mailchimp
  	 * but it has to be the same used in both tests
  	 * and several developers should be able to run the tests at the same time.
  	 * So make sure MAILCHIMP_TEST_EMAIL can take wildcard on the leftside of the "@"
  	 * @see https://stackoverflow.com/questions/41160763/not-allowing-more-signups-for-now-in-mailchimp-api
  	 */
  	public function testItShouldSubscribeUser()
  	{

  		$today = date('ymd');
  		$fork = getenv('FORK') || "up";
  		$current_pid = getmypid();
  		$api_key = getenv("MAILCHIMP_API_KEY");
  		$list_id = getenv("MAILCHIMP_LIST_ID");
      $this->assertNotNull($api_key);
      $this->assertNotNull($list_id);

  		$email= $fork.$today.$current_pid."_".getenv("MAILCHIMP_TEST_EMAIL") ;
  		$service = new NewsletterService($api_key, $list_id);
  		$result = $service->addToMailing($email);
  		$this->assertTrue( $result );
  	}

  	/**
  	 * Test that we can unsubscribe an email address
  	 *
  	 * The test email address need to be different each time otherwise it will be banned by Mailchimp
  	 * but it has to be the same used in both tests
  	 * and several developers should be able to run the tests at the same time
  	 * So make sure MAILCHIMP_TEST_EMAIL can take wildcard on the leftside of the "@"
	   * @see https://stackoverflow.com/questions/41160763/not-allowing-more-signups-for-now-in-mailchimp-api
  	 */
  	public function testItShouldUnsubscribeUser()
  	{
  		$api_key = getenv("MAILCHIMP_API_KEY");
  		$list_id = getenv("MAILCHIMP_LIST_ID");

  		$email= $fork.$today.$current_pid."_".getenv("MAILCHIMP_TEST_EMAIL") ;
  		$service = new NewsletterService($api_key, $list_id);
  		$result = $service->removeFromMailing($email);
  		$this->assertTrue( $result );
  	}

  	/**
  	 * Test that Application component is configured properly
  	 *
  	 */
  	public function testItsAnApplicationComponent()
  	{
  		$this->assertTrue( Yii::app()->newsletter->getMailingListInfo() );
  	}


}

?>
