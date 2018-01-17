Feature: Adjusting how an author's name is displayed on a dataset page
As a paper author,
I want to be able to set up how my name appears on gigadb paper's page.
So that it appears correctly on the dataset page.


Scenario: Preview names when editing author's details
	Given I sign in as an admin
	And I am on "/adminAuthor/update/id/19"
	When I fill in "Author_surname" with "Poe"
	And I fill in "Author_first_name" with "Edgar"
	And I fill in "Author_middle_name" with "Allan"
	And I press "Save"
	Then I should see "Poe EA"