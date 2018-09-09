@login @normal-login @ok-docker
Feature: sign in into the gigadb website with my Gigadb credentials
	AS a user,
	I WANT TO sign in to the gigadb website with my credentials
	SO THAT I can upload and manage the datasets for my papers


@gigadb @ok @done
Scenario: Logging in into gigadb website with the credentials of an existing account
	Given I have a gigadb account with "user" role
	When I am on "/site/login"
	And I fill in "LoginForm_username" with "user@gigadb.org"
	And I fill in "LoginForm_password" with "gigadb"
	And I press "Login"
	Then I'm logged in into the Gigadb web site


