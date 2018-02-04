Feature: a curator can fill in user id in an author record
	As a curator,
	I want to connect a user identity to an author record
	So that I can enable gigadb users direct access to the dataset they have authored

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.sql" data
	And default admin user exists
	When I go to "/dataset/100002"
	Then I should see "Genomic data from Adelie penguin (Pygoscelis adeliae)"

@ok
Scenario: populate user identity field when creating an author
	Given default admin user exists
	And I sign in as an admin
	And I am on "/adminAuthor/create"
	When I fill in "Surname" with "Tano"
	And I fill in "First Name" with "Ahsoka"
	And I fill in "Middle Name" with "Fulcrum"
	And I fill in "Gigadb User" with "345"
	And press "Create"
	Then the response should contain "Gigadb User"
	And the response should contain "345"
	And the response should contain "Tano AF"

@ok
Scenario: populate user identity field when updating an author
	Given default admin user exists
	And I sign in as an admin
	And I am on "/adminAuthor/update/id/3791"
	And I fill in "Gigadb User" with "345"
	And press "Save"
	Then I should be on "/adminAuthor/view/id/3791"
	And I should see "Gigadb User"
	And I should see "345"

@wip
Scenario: From user view, find an author to attach
	Given default admin user exists
	And I sign in as an admin
	And I am on "/user/view/id/345"
	When I press "Attach an author to this user"
	Then I should be on "/adminAuthor/admin"
	And I should see "Click on a row to link that author with user John Smith"
	And I should see "Cancel attaching author"

@wip
Scenario: From user edit form, find an author to attach
	Given default admin user exists
	And I sign in as an admin
	And I am on "/user/update/id/345"
	When I press "Attach an author to this user"
	Then I should be on "/adminAuthor/admin"
	And I should see "Click on a row to link that author with user John Smith"
	And I should see "Cancel attaching author"


@wip
Scenario: Attach an author
	Given default admin user exists
	And I sign in as an admin
	And I have initiated the search of an author for Gigadb User with ID "345" 
	And I am on "/adminAuthor/admin"
	When I click the row of ID "3790"
	Then I should be on "/adminAuthor/view/id/3790"
	And I should see "Gigadb User"
	And I should see "345"


@wip
Scenario: Cancel attaching an author
	Given default admin user exists
	And I sign in as an admin
	And I have initiated the search of an author for Gigadb User with ID "345" 
	And I am on "/adminAuthor/admin"
	When I follow "Cancel attaching author"
	Then I should be on "/adminAuthor/admin"
	And I should not see "Click on a row to link that author with user John Smith"
	And I should not see "Cancel attaching author"


