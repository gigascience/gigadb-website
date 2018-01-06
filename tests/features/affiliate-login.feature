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


	@ok @javascript @facebook
	Scenario: I sign in with Facebook with no existing Gigadb account
		Given I have a "Facebook" account
		And The "Facebook" account has not authorised login to GigaDB web site
		But I don't have a Gigadb account for my "Facebook" account email
		When I am on "/site/chooseLogin"
		And I click on the "Facebook" button
		And I authorise Gigadb for "Facebook"
		Then I should be redirected from "Facebook"
		And I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "Facebook" details

	@ok @javascript @google
	Scenario: I sign in with Google with no existing Gigadb account
		Given I have a "Google" account
		But I don't have a Gigadb account for my "Google" account email
		When I am on "/site/chooseLogin"
		And I click on the "Google" button
		And I authorise Gigadb for "Google"
		Then I should be redirected from "Google"
		And I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "Google" details

	@ok @twitter
	Scenario: I sign in with Twitter with no existing Gigadb account
		Given I have a "Twitter" account
		But I don't have a Gigadb account for my "Twitter" account email
		When I am on "/site/chooseLogin"
		And I click on the "Twitter" button
		And I authorise Gigadb for "Twitter"
		Then I should be redirected from "Twitter"
		And I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "Twitter" details

	@ok @linkedin
	Scenario: I sign in with LinkedIn with no existing Gigadb account
		Given I have a "LinkedIn" account
		But I don't have a Gigadb account for my "LinkedIn" account email
		When I am on "/site/chooseLogin"
		And I click on the "LinkedIn" button
		And I authorise Gigadb for "LinkedIn"
		Then I should be redirected from "LinkedIn"
		And I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "LinkedIn" details

	@ok @orcid @javascript
	Scenario: I sign in with ORCID with no existing Gigadb account
		Given I have a "ORCID" account
		But I don't have a Gigadb account for my "ORCID" account email
		When I am on "/site/chooseLogin"
		And I click on the "ORCID" button
		And I authorise Gigadb for "ORCID"
		Then I should be redirected from "ORCID"
		And I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "ORCID" details

	@ok @facebook @javascript
	Scenario: I have a Gigadb account and I sign in with my "Facebook" credentials
		Given I have a "Facebook" account
		And The "Facebook" account has not authorised login to GigaDB web site
		And I have a Gigadb account for my "Facebook" account email
		When I am on "/site/chooseLogin"
		And I click on the "Facebook" button
		And I authorise Gigadb for "Facebook"
		Then I should be redirected from "Facebook"
		And I'm logged in into the Gigadb web site
		And no new gigadb account is created for my "Facebook" account email

	@ok @javascript @google
	Scenario: I have a Gigadb account and I sign in with my "Google" credentials
		Given I have a "Google" account
		And I have a Gigadb account for my "Google" account email
		When I am on "/site/chooseLogin"
		And I click on the "Google" button
		And I authorise Gigadb for "Google"
		Then I should be redirected from "Google"
		And I'm logged in into the Gigadb web site
		And no new gigadb account is created for my "Google" account email

	@ok @twitter
	Scenario: I have a Gigadb account and I sign in with my "Twitter" credentials
		Given I have a "Twitter" account
		And I have a Gigadb account for my "Twitter" account email
		When I am on "/site/chooseLogin"
		And I click on the "Twitter" button
		And I authorise Gigadb for "Twitter"
		Then I should be redirected from "Twitter"
		And I'm logged in into the Gigadb web site
		And no new gigadb account is created for my "Twitter" account email

	@ok @linkedin
	Scenario: I have a Gigadb account and I sign in with my "LinkedIn" credentials
		And I have a "LinkedIn" account
		And I have a Gigadb account for my "LinkedIn" account email
		When I am on "/site/chooseLogin"
		And I click on the "LinkedIn" button
		And I authorise Gigadb for "LinkedIn"
		Then I should be redirected from "LinkedIn"
		And I'm logged in into the Gigadb web site
		And no new gigadb account is created for my "LinkedIn" account email

	@orcid @javascript
	Scenario: I have a Gigadb account associated with my ORCID id and I sign in with my "ORCID" credentials
		Given I have a "ORCID" account
		And I have a Gigadb account for my "ORCID" uid
		When I am on "/site/chooseLogin"
		And I click on the "ORCID" buttons
		And I authorise Gigadb for "ORCID"
		Then I should be redirected from "ORCID"
		And I'm logged in into the Gigadb web site
		And no new gigadb account is created for my "ORCID" account email

	@ok @differentemails @facebook @javascript
	Scenario: I sign in with my "Facebook" credentials and I have a gigadb account with a different email address
		Given I have a "Facebook" account
		And The "Facebook" account has not authorised login to GigaDB web site
		And I have a Gigadb account with a different email
		When I am on "/site/chooseLogin"
		And I click on the "Facebook" button
		And I authorise Gigadb for "Facebook"
		Then I should be redirected from "Facebook"
		And I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "Facebook" details

	@ok @differentemails @google @javascript
	Scenario: I sign in with my "Google" credentials and I have a gigadb account with a different email address
		Given I have a "Google" account
		And I have a Gigadb account with a different email
		When I am on "/site/chooseLogin"
		And I click on the "Google" button
		And I authorise Gigadb for "Google"
		Then I should be redirected from "Google"
		And I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "Google" details

	@ok @differentemails @twitter
	Scenario: I sign in with my "Twitter" credentials and I have a gigadb account with a different email address
		Given I have a "Twitter" account
		And I have a Gigadb account with a different email
		When I am on "/site/chooseLogin"
		And I click on the "Twitter" button
		And I authorise Gigadb for "Twitter"
		Then I should be redirected from "Twitter"
		And I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "Twitter" details

	@ok @differentemails @linkedin
	Scenario: I sign in with my "LinkedIn" credentials and I have a gigadb account with a different email address
		Given I have a "LinkedIn" account
		And I have a Gigadb account with a different email
		When I am on "/site/chooseLogin"
		And I click on the "LinkedIn" button
		And I authorise Gigadb for "LinkedIn"
		Then I should be redirected from "LinkedIn"
		And I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "LinkedIn" details

	@ok @differentemails @orcid @javascript
	Scenario: I sign in with my "ORCID" credentials and I have a gigadb account with a different email address
		Given I have a "ORCID" account
		And I have a Gigadb account with a different email
		When I am on "/site/chooseLogin"
		And I click on the "ORCID" button
		And I authorise Gigadb for "ORCID"
		Then I should be redirected from "ORCID"
		And I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "ORCID" details

