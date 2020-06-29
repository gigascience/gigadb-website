<?php

/**
 * browser automation steps for login to the web site
 *
 * This trait is to be used in functional tests
 * This trait's function is to ensure browser mediation is performed only in one place as much as possible.
 * Making it easier to change browser mediation framework as needed
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
trait BrowserSignInSteps
{
	public function loginToWebSiteWithSessionAndCredentialsThenAssert($email,$password,$to_assert)
	{

        $this->session->visit("http://gigadb.dev/site/admin");
        $this->session->getPage()->fillField("LoginForm_username", $email);
        $this->session->getPage()->fillField("LoginForm_password", $password);
        $this->session->getPage()->pressButton("Login");

        $this->assertTrue($this->session->getPage()->hasContent($to_assert));
	}

	public function nonLoggedInAndCannotVisitAdminThenAssert($email,$password,$to_assert)
    {
        $this->session->visit("http://gigadb.dev/site/admin");
        $this->session->getPage()->fillField("LoginForm_username", $email);
        $this->session->getPage()->fillField("LoginForm_password", $password);
        $this->session->getPage()->pressButton("Login");

        $this->assertFalse($this->session->getPage()->hasContent($to_assert));

    }
}
