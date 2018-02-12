@datasets-on-user-profile @issue-60
Feature: shows datasets authored by a user on his/her profile
	As a gigadb user,
	I want to see on my profile the datasets that I have authored
	So that I can manage them conveniently from one place

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.sql" data
	And user "joy_fox" is loaded

@ok
Scenario: user is associated to an author of existing dataset
	Given I sign in as a user
	And I am linked to author "Zhang, G"
	When I am on "/user/view_profile"
	Then I should see "Your Authored Datasets"
	And I should see "Genomic data from Adelie penguin (Pygoscelis adeliae)"

@ok
Scenario: no association with dataset author made
	Given I sign in as a user
	When I am on "/user/view_profile"
	Then I should not see "10.5524"

@ok
Scenario: user is associated to an author with no existing dataset
	Given I sign in as a user
	And I am linked to author "Yue, Z"
	When I am on "/user/view_profile"
	Then I should see "Your Authored Datasets"
	And I should not see "10.5524"

@ok
Scenario: user is associated to two authors with datasets
	Given I sign in as a user
	And I am linked to author "Pan, S"
	And I am linked to author "Zhang, G"
	When I am on "/user/view_profile"
	Then I should see "Your Authored Datasets"
	And I should see "Genomic data from Adelie penguin (Pygoscelis adeliae)"
	And I should see "Genome data from foxtail millet (Setaria italica)"
