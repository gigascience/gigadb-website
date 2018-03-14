@issue-49 @merging-two-authors @javascript
Feature: Merging two duplicate authors
	In order to reduce redundancy and to increase datasets interlinking
	As an admin user
	I want to connect two author records as identical

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.sql" data
	And default admin user exists
	When I go to "/dataset/100002"
	Then I should see "Genomic data from Adelie penguin (Pygoscelis adeliae)"


Scenario: From author edit, merge with another author button leads to author table
	Given I sign in as an admin
	And I am on "/adminAuthor/update/id/3791"
	When I follow "Merge with another author"
	And I wait "2" seconds
	Then the response should contain "Click a row to merge Zhang G with that author" 

Scenario: From a table row with an author to merge, pick another author to merge with 
	And I have initiated the search of an author to merge with author ID "3791"
	Given I sign in as admin
	When I click on the row for author id "3794"
	And I follow "Merge Zhang G to that author"
	And I wait "2" seconds
	And I click on "Confirm merging"
	And I wait for "2" seconds
	Then I should be on "/adminAuthor/view/3791"
	And I should see "Author merged with author Pan S"
	And I should see "Unmerge"
	And I should see "Go to the other author's view"

Scenario: Abort the merge from the popup confirmation box from author table
	And I have initiated the search of an author to merge with author ID "3791"
	Given I sign in as admin
	When I click on the row for author id "3794"
	And I follow "Merge Zhang G to that author"
	And I wait "2" seconds
	And I click on "Abort the merge"
	And I wait for "2" seconds
	Then I should be on "/adminAuthor/update/id/3791"
	And I should not see "Author merged with author"

Scenario: Abort the merge from the close button on the message on the author table
	And I have initiated the search of an author to merge with author ID "3791"
	Given I sign in as admin
	When I click the close button on the message
	And I wait "2" seconds
	Then I should be on "/adminAuthor/update/id/3791"
	And I should not see "Author merged with author"

Scenario: On author view, display all authors that have been merged
	Given author "3791" is merged with "3792"
	And author "3791" is merged with "3793"
	And author "3792" is merged with "3795"
	And I sign in as admin
	When I go to "/adminAuthor/view/id/3791"
	Then I should see "Author merged with author Cheng S"
	And I should see "Author merged with author Liu X"


