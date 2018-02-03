Feature: a curator can fill in user id in an author record
	As a curator,
	I want to connect a user identity to an author record
	So that I can enable gigadb users direct access to the dataset they have authored

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.sql" data
	And default admin user exists
	When I am on "/dataset/100002"
	Then I should see "Genomic data from Adelie penguin (Pygoscelis adeliae)"


Scenario: populate user identity field when creating an author
	Given default admin user exists
	And I sign in as an admin
	And I am on "/adminAuthor/create"
	When I fill in "Surname" with "Tano"
	And I fill in "First Name" with "Ashoka"
	And I fill in "Middle Name" with "Fulcrum"
	And I fill in "User identity" with "345"
	And press "Create"
	Then the response should contain "User Identity"
	And the response should contain "345"

Scenario: populate user identity field when updating an author
	Given default admin user exists
	And I sign in as an admin
	And I am on "/adminAuthor/update/id/3791"
	And I fill in "User identity" with "345"
	And press "Save"
	Then the response should contain "User Identity"
	And the response should contain "345"
	And I should be on "/adminAuthor/view/id/3791"
