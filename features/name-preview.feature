@edit-display-name @issue-81 @ok-docker
Feature: Adjusting how an author's name is displayed on a dataset page
As a paper author,
I want to be able to set up how my name appears on gigadb paper's page.
So that it appears correctly on the dataset page.

@ok
Scenario: appropriate test environment
	Given Gigadb web site is loaded with production-like data
	And an admin user exists
	When I am on "/dataset/100039"


@ok
Scenario: If custom display name field is empty, save calculated value 
	Given I sign in as an admin
	And I am on "/adminAuthor/update/id/19"
	When I fill in "Author_surname" with "Poe"
	And I fill in "Author_first_name" with "Edgar"
	And I fill in "Author_middle_name" with "Allan"
	And I press "Save"
	Then I should see "Poe EA"

@ok
Scenario: when display name edited, save it instead of calculated value
	Given I sign in as an admin
	And I am on "/adminAuthor/update/id/19"
	When I fill in "Author_surname" with "Poe"
	And I fill in "Author_first_name" with "Edgar"
	And I fill in "Author_middle_name" with "Allan"
	And I fill in "Author_custom_name" with "PEA"
	And I press "Save"
	Then I should see "PEA"
