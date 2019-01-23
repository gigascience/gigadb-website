<?php


class NewsletterServiceTest extends CTestCase
{

	/**
  	 * Test that only valid email can be added to subscriber list
  	 *
  	 * @dataProvider provideEmails
  	 */
  	public function testItShouldSubscribeUser($params, $how_many_times, $expected_api_call)
  	{

  		$api_key = "abc123abc123abc123abc123abc123-us1" ;
  		$list_id = "123456" ;

  		$mailchimp = $this->getMockBuilder('\DrewM\MailChimp\MailChimp')
					  	 ->setConstructorArgs([$api_key])
                         ->setMethods(['post','success'])
                         ->getMock();
  		$service = new NewsletterService($api_key, $list_id, $mailchimp);

  		$mailchimp->expects($this->exactly($how_many_times))
                 ->method('post')
                 ->with("lists/$list_id/members", $expected_api_call);

        $mailchimp->expects($this->exactly($how_many_times))
        		->method('success')
        		->willReturn(true);

  		$service->addToMailing($params[0], $params[1], $params[2]);

  	}

	/**
  	 * Test that email can be removed from subscriber list
  	 *
  	 * @dataProvider provideEmails
  	 */
  	public function testItShouldUnsubscribeUser()
  	{
  		$api_key = "abc123abc123abc123abc123abc123-us1" ;
  		$list_id = "123456" ;
  		$email = "foo@bar.com";
  		$hash_string = "sfskdfhsdgsdg";

  		$mailchimp = $this->getMockBuilder('\DrewM\MailChimp\MailChimp')
					  	 ->setConstructorArgs([$api_key])
                         ->setMethods(['subscriberHash','delete'])
                         ->getMock();
  		$service = new NewsletterService($api_key, $list_id, $mailchimp);

  		$mailchimp->expects($this->once())
                 ->method('subscriberHash')
                 ->with($email)
                 ->willReturn($hash_string);

        $mailchimp->expects($this->once())
                 ->method('delete')
                 ->with("lists/$list_id/members/$hash_string");

  		$service->removeFromMailing($email);
  	}

	/**
  	 * Test that it returns info about the list
  	 *
  	 */
  	public function testItShouldGetMailingListInfo()
  	{
  		$api_key = "abc123abc123abc123abc123abc123-us1" ;
  		$list_id = "123456" ;
  		$email = "foo@bar.com";
  		$hash_string = "sfskdfhsdgsdg";

  		$mailchimp = $this->getMockBuilder('\DrewM\MailChimp\MailChimp')
					  	 ->setConstructorArgs([$api_key])
                         ->setMethods(['get'])
                         ->getMock();
  		$service = new NewsletterService($api_key, $list_id, $mailchimp);

        $mailchimp->expects($this->once())
                 ->method('get')
                 ->with("lists/$list_id");

  		$service->getMailingListInfo();
  	}

  	public function provideEmails() {
  		return [
  			"valid email and no details" => [
  										["foo@hotmail.com"],
  										1,
  										['email_address' => 'foo@hotmail.com', 'status' => 'subscribed'],
  									],
  			"valid email and name" => [
  										["foo@hotmail.com", "Foo", "Bar"],
  										1,
  										['email_address' => 'foo@hotmail.com', 'merge_fields' => ['FNAME'=>'Foo', 'LNAME'=>'Bar'], 'status' => 'subscribed'],
  									],
  			"obvious invalid email" => [
  										["foobar"],
  										0,
  										['email_address' => 'foobar', 'status' => 'subscribed'],
  									],
  			"subtle invalid email" => [ # invalid according to RFC 822 (note: although acceptable according to RFC 5321)
  										['"much.more unusual"@example.com'],
  										0,
  										['email_address' => '"much.more unusual"@example.com', 'status' => 'subscribed'],
  									],
  			"Email domain doesn't exist" => [
  										["james@speed.bump"],
  										0,
  										['email_address' => 'james@speed.bump', 'status' => 'subscribed'],
  									],
  	  		"Internationalized email address" => [
  										["ουτοπία@δπθ.gr"],
  										1,
  										['email_address' => 'xn--kxae4bafwg@xn--pxaix.gr', 'status' => 'subscribed'],
  									],
  		];
  	}

}