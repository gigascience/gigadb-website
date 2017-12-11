Feature: sign in into the gigadb website with my social media credentials
AS an author,
I WANT TO sign in to the gigadb website with my social media credentials
SO THAT I can upload and manage the datasets for my papers

	Background:
		Given Gigadb has a "Facebook" API keys
		And Gigadb has a "Google" API keys
		And Gigadb has a "Twitter" API keys
		And Gigadb has a "LinkedIn" API keys
		And Gigadb has a "Orcid" API keys


	@wip
	Scenario: I sign in with Facebook with no existing Gigadb account
		Given I have a "Facebook" account
		But I don't have a Gigadb account
		When I navigate to "/site/chooseLogin"
		And I click on the "Facebook" button
		And I authorise Gigadb for "Facebook"
		Then a new Gigadb account is created
		And I'm logged in into that account
		And the email I used for "Facebook" is used for that account

	Scenario: I sign in with Google with no existing Gigadb account
		Given I have a "Google" account
		But I don't have a Gigadb account
		When I navigate to "/site/chooseLogin"
		And I click on the "Google" button
		And I authorise Gigadb for "Google"
		Then a new Gigadb account is created
		And I'm logged in into that account
		And the email I used for "Google" is used for that account

	Scenario: I sign in with Twitter with no existing Gigadb account
		Given I have a "Twitter" account
		But I don't have a Gigadb account
		When I navigate to "/site/chooseLogin"
		And I click on the "Twitter" button
		And I authorise Gigadb for "Twitter"
		Then a new Gigadb account is created
		And I'm logged in into that account
		And the email I used for "Twitter" is used for that account

	Scenario: I sign in with LinkedIn with no existing Gigadb account
		Given I have a "LinkedIn" account
		But I don't have a Gigadb account
		When I navigate to "/site/chooseLogin"
		And I click on the "LinkedIn" button
		And I authorise Gigadb for "LinkedIn"
		Then a new Gigadb account is created
		And I'm logged in into that account
		And the email I used for "LinkedIn" is used for that account

	Scenario: I sign in with ORCID with no existing Gigadb account
		Given I have a "ORCID" account
		But I don't have a Gigadb account
		When I navigate to "/site/chooseLogin"
		And I click on the "ORCID" button
		And I authorise Gigadb for "ORCID"
		Then a new Gigadb account is created
		And I'm logged in into that account
		And the email I used for "ORCID" is used for that account

	Scenario: I have a Gigadb account and I sign in with my "Facebook" credentials
		Given I have a Gigadb account
		And I have a "Facebook" account
		And email addresses for those accounts match
		When I navigate to "/site/chooseLogin"
		And I click on the "Facebook" button
		And I authorise Gigadb for "Facebook"
		Then I'm logged in into my existing account
		And no new gigadb account is created

	Scenario: I have a Gigadb account and I sign in with my "Google" credentials
		Given I have a Gigadb account
		And I have a "Google" account
		And email addresses for those accounts match
		When I navigate to "/site/chooseLogin"
		And I click on the "Google" button
		And I authorise Gigadb for "Google"
		Then I'm logged in into my existing account
		And no new gigadb account is created

	Scenario: I have a Gigadb account and I sign in with my "Twitter" credentials
		Given I have a Gigadb account
		And I have a "Twitter" account
		And email addresses for those accounts match
		When I navigate to "/site/chooseLogin"
		And I click on the "Twitter" button
		And I authorise Gigadb for "Twitter"
		Then I'm logged in into my existing account
		And no new gigadb account is created

	Scenario: I have a Gigadb account and I sign in with my "LinkedIn" credentials
		Given I have a Gigadb account
		And I have a "LinkedIn" account
		And email addresses for those accounts match
		When I navigate to "/site/chooseLogin"
		And I click on the "LinkedIn" buttons
		And I authorise Gigadb for "LinkedIn"
		Then I'm logged in into my existing account
		And no new gigadb account is created

	Scenario: I have a Gigadb account and I sign in with my "ORCID" credentials
		Given I have a Gigadb account
		And I have a "ORCID" account
		And email addresses for those accounts match
		When I navigate to "/site/chooseLogin"
		And I click on the "ORCID" buttons
		And I authorise Gigadb for "ORCID"
		Then I'm logged in into my existing account
		And no new gigadb account is created

	Scenario: I sign in with my "Facebook" credentials and I have a gigadb account with a different email address
		Given I have a Gigadb account
		And I have a "Facebook" account
		But email addresses for those accounts do not match
		When I navigate to "/site/chooseLogin"
		And I click on the "Facebook" button
		And I authorise Gigadb for "Facebook"
		Then a new Gigadb account is created
		And I'm logged in into that account
		And the email I used for "Facebook" is used for that account


	Scenario: I sign in with my "Google" credentials and I have a gigadb account with a different email address
		Given I have a Gigadb account
		And I have a "Google" account
		But email addresses for those accounts do not match
		When I navigate to "/site/chooseLogin"
		And I click on the "Google" button
		And I authorise Gigadb for "Google"
		Then a new Gigadb account is created
		And I'm logged in into that account
		And the email I used for "Google" is used for that account

	Scenario: I sign in with my "Twitter" credentials and I have a gigadb account with a different email address
		Given I have a Gigadb account
		And I have a "Twitter" account
		But email addresses for those accounts do not match
		When I navigate to "/site/chooseLogin"
		And I click on the "Twitter" button
		And I authorise Gigadb for "Twitter"
		Then a new Gigadb account is created
		And I'm logged in into that account
		And the email I used for "Twitter" is used for that account

	Scenario: I sign in with my "LinkedIn" credentials and I have a gigadb account with a different email address
		Given I have a Gigadb account
		And I have a "LinkedIn" account
		But email addresses for those accounts do not match
		When I navigate to "/site/chooseLogin"
		And I click on the "LinkedIn" button
		And I authorise Gigadb for "LinkedIn"
		Then a new Gigadb account is created
		And I'm logged in into that account
		And the email I used for "LinkedIn" is used for that account

	Scenario: I sign in with my "Orcid" credentials and I have a gigadb account with a different email address
		Given I have a Gigadb account
		And I have a "Orcid" account
		But email addresses for those accounts do not match
		When I navigate to "/site/chooseLogin"
		And I click on the "Orcid" button
		And I authorise Gigadb for "Orcid"
		Then a new Gigadb account is created
		And I'm logged in into that account
		And the email I used for "Orcid" is used for that account

