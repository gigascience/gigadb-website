@datasets-on-user-profile @issue-60
Feature: shows datasets authored by a user on his/her profile
	As a gigadb user,
	I want to see on my profile the datasets that I have authored
	So that I can manage them conveniently from one place

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.sql" data
	And default user exists


Scenario: user is associated to an author of existing dataset
	Given I sign in as a user
	And I am linked to author "Zhang, G"
	When I am on "/user/view_profile"
	Then I should see "Your Authored Datasets"
	And I should see "Genomic data from Adelie penguin (Pygoscelis adeliae)"

Scenario: no association with dataset author made
	Given I sign in as a user
	When I am on "/user/view_profile"
	Then I should not see "Your Authored Datasets"

Scenario: user has a pending claim on a dataset author
	Given I sign in as a user
	When I am on "/user/view_profile"
	Then I should not see "Your Authored Datasets"


Scenario: user is associated to an author with no existing dataset
	Given I sign in as a user
	And I am linked to author "Yue, Z"
	When I am on "/user/view_profile"
	Then I should see "Your Authored Datasets"
	And I should not see any results under the section "Your Authored Datasets"