@login @affiliate-login @issue-64 @ok-docker
Feature: sign in into the gigadb website with my social media credentials
AS an author,
I WANT TO sign in to the gigadb website with my social media credentials
SO THAT I can upload and manage the datasets for my papers

	Background:
		Given test users are loaded
		And Gigadb has a "Facebook" API keys
		And Gigadb has a "Google" API keys
		And Gigadb has a "Twitter" API keys
		And Gigadb has a "LinkedIn" API keys
		And Gigadb has a "Orcid" API keys


	@ok @javascript @facebook @done @first
	Scenario: I sign in with Facebook with no existing Gigadb account
		Given I have a "Facebook" account
		And The "Facebook" account has not authorised login to GigaDB web site
		But I don't have a Gigadb account for my "Facebook" account email
		When I am on "/site/login"
		And I click on the "Facebook" button
		And I sign in to "Facebook"
		And I authorise gigadb for "Facebook"
		Then I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "Facebook" details

	@ok @javascript @google @done
	Scenario: I sign in with Google with no existing Gigadb account
		Given I have a "Google" account
		But I don't have a Gigadb account for my "Google" account email
		When I am on "/site/login"
		And I click on the "Google" button
		And I sign in to "Google"
		And I authorise gigadb for "Google"
		Then I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "Google" details

	@ok @twitter @done @first-non-js
	Scenario: I sign in with Twitter with no existing Gigadb account
		Given I have a "Twitter" account
		But I don't have a Gigadb account for my "Twitter" account email
		When I am on "/site/login"
		And I click on the "Twitter" button
		And I sign in to "Twitter"
		And I authorise gigadb for "Twitter"
		Then I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "Twitter" details

	@ok @linkedin @javascript @done
	Scenario: I sign in with LinkedIn with no existing Gigadb account
		Given I have a "LinkedIn" account
		But I don't have a Gigadb account for my "LinkedIn" account email
		When I am on "/site/login"
		And I click on the "LinkedIn" button
		And I sign in to "LinkedIn"
		And I authorise gigadb for "LinkedIn"
		Then I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "LinkedIn" details

	#@ok @orcid @javascript @done
	#Scenario: I sign in with ORCID with no existing Gigadb account
		#Given I have a "Orcid" account
		#But I don't have a Gigadb account for my "Orcid" account email
		#When I am on "/site/login"
		#And I click on the "ORCID" button
		#And I sign in to "Orcid"
		#And I authorise gigadb for "Orcid"
		#Then I'm logged in into the Gigadb web site
		#And a new Gigadb account is created with my "Orcid" details

	@ok @facebook @javascript @done
	Scenario: I have a Gigadb account and I sign in with my "Facebook" credentials
		Given I have a "Facebook" account
		And The "Facebook" account has not authorised login to GigaDB web site
		And I have a Gigadb account for my "Facebook" account email
		When I am on "/site/login"
		And I click on the "Facebook" button
		And I sign in to "Facebook"
		And I authorise gigadb for "Facebook"
		Then I'm logged in into the Gigadb web site
		And no new gigadb account is created for my "Facebook" account email

	@ok @javascript @google @done
	Scenario: I have a Gigadb account and I sign in with my "Google" credentials
		Given I have a "Google" account
		And I have a Gigadb account for my "Google" account email
		When I am on "/site/login"
		And I click on the "Google" button
		And I sign in to "Google"
		And I authorise gigadb for "Google"
		Then I'm logged in into the Gigadb web site
		And no new gigadb account is created for my "Google" account email

	@ok @twitter @done
	Scenario: I have a Gigadb account and I sign in with my "Twitter" credentials
		Given I have a "Twitter" account
		And I have a Gigadb account for my "Twitter" account email
		When I am on "/site/login"
		And I click on the "Twitter" button
		And I sign in to "Twitter"
		And I authorise gigadb for "Twitter"
		Then I'm logged in into the Gigadb web site
		And no new gigadb account is created for my "Twitter" account email

	@ok @linkedin @javascript @done
	Scenario: I have a Gigadb account and I sign in with my "LinkedIn" credentials
		And I have a "LinkedIn" account
		And I have a Gigadb account for my "LinkedIn" account email
		When I am on "/site/login"
		And I click on the "LinkedIn" button
		And I sign in to "LinkedIn"
		And I authorise gigadb for "LinkedIn"
		Then I'm logged in into the Gigadb web site
		And no new gigadb account is created for my "LinkedIn" account email

	@ok @orcid @javascript @done
	Scenario: I have a Gigadb account associated with my ORCID id and I sign in with my "ORCID" credentials
		Given I have a "Orcid" account
		And I have a Gigadb account for my "Orcid" uid
		When I am on "/site/login"
		And I click on the "ORCID" button
		And I sign in to "Orcid"
		And I authorise gigadb for "Orcid"
		Then I'm logged in into the Gigadb web site
		And no new gigadb account is created for my "Orcid" account email

	@ok @differentemails @facebook @javascript @done
	Scenario: I sign in with my "Facebook" credentials and I have a gigadb account with a different email address
		Given I have a "Facebook" account
		And The "Facebook" account has not authorised login to GigaDB web site
		And I have a Gigadb account with a different email
		When I am on "/site/login"
		And I click on the "Facebook" button
		And I sign in to "Facebook"
		And I authorise gigadb for "Facebook"
		Then I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "Facebook" details

	@ok @differentemails @google @javascript @done
	Scenario: I sign in with my "Google" credentials and I have a gigadb account with a different email address
		Given I have a "Google" account
		And I have a Gigadb account with a different email
		When I am on "/site/login"
		And I click on the "Google" button
		And I sign in to "Google"
		And I authorise gigadb for "Google"
		Then I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "Google" details

	@ok @differentemails @twitter @done
	Scenario: I sign in with my "Twitter" credentials and I have a gigadb account with a different email address
		Given I have a "Twitter" account
		And I have a Gigadb account with a different email
		When I am on "/site/login"
		And I click on the "Twitter" button
		And I sign in to "Twitter"
		And I authorise gigadb for "Twitter"
		Then I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "Twitter" details

	@ok @differentemails @linkedin  @javascript @done
	Scenario: I sign in with my "LinkedIn" credentials and I have a gigadb account with a different email address
		Given I have a "LinkedIn" account
		And I have a Gigadb account with a different email
		When I am on "/site/login"
		And I click on the "LinkedIn" button
		And I sign in to "LinkedIn"
		And I authorise gigadb for "LinkedIn"
		Then I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "LinkedIn" details

	@ok @differentemails @orcid @javascript @done
	Scenario: I sign in with my "Orcid" credentials and I have a gigadb account with a different email address
		Given I have a "Orcid" account
		And I have a Gigadb account with a different email
		When I am on "/site/login"
		And I click on the "ORCID" button
		And I sign in to "Orcid"
		And I authorise gigadb for "Orcid"
		Then I'm logged in into the Gigadb web site
		And a new Gigadb account is created with my "Orcid" details

