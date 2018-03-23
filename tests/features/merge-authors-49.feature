@issue-49 @merging-two-authors @javascript
Feature: Merging duplicate authors
	In order to reduce data duplication and to increase datasets interlinking
	As an admin user
	I want to merge author records that are identical

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.sql" data
	And default admin user exists
	When I go to "/dataset/100002"
	Then I should see "Genomic data from Adelie penguin (Pygoscelis adeliae)"

Scenario: On author edit form, there is a button to start the merging with another author
	Given I sign in as an admin
	When I go to "/adminAuthor/update/id/3791"
	Then I should see "Merge with an author"

Scenario: Presssing the merge an author button leads to author table and then merging of an author
	Given I sign in as an admin
	And I am on "/adminAuthor/update/id/3791"
	When I follow "Merge with author"
	And I wait "2" seconds
	And I click on the row for author id "3794"
	And I wait "1" seconds
	And A dialog box reads "Confirm merging these two authors?"
	And I should see "Zhang Guojie"
	And I should see "Lambert David M"
	And I should see "ORCID: n/a"
	And I should see "ORCID: 0000-0002-5486-853Z"
	And I follow "Yes, merge with selected author"
	And I wait "1" seconds
	Then I should be on "/admin/Author/view/id/3791"
	And I should see "This author is merged with author Pan S"

Scenario: Abort a merge from the popup confirmation box
	Given I sign in as an admin
	And I am on "/adminAuthor/update/id/3791"
	When I follow "Merge with author"
	And I wait "2" seconds
	And I click on the row for author id "3794"
	And I wait "1" seconds
	And A dialog box reads "Confirm merging Zhang G with Pan S ?"
	And I follow "No, abort merging"
	And I wait "1" seconds
	Then I should be on "/admin/Author/view/id/3791"
	And I should not see "This author is merged with author"


Scenario: There is an unmerge button to disconnect two authors from an author edit form
	Given author "3791" is merged with author "3794"
	And I sign in as an admin
	When I go to "/adminAuthor/update/id/3791"
	Then I should see "This author is merged with author Pan S"
	And I should see "Unmerge"

Scenario: Cannot merge an author with himself
	Given I sign in as an admin
	And I am on "/adminAuthor/update/id/3791"
	When I follow "Merge with author"
	And I wait "2" seconds
	And I click on the row for author id "3791"
	And I wait "1" seconds
	And A dialog box reads "Confirm merging Zhang G with Pan S ?"
	And I follow "Yes, merge with selected author"
	Then I should see "Cannot merge with self. Choose another author to merge with"

Scenario: On author view, indication of merging is bi-directional
	Given author "3791" is merged with author "3794"
	And I sign in as an admin
	When I go to "/adminAuthor/view/id/3794"
	Then I should see "This author is merged with author Zhang G"

Scenario: On author edit form, indication of merging is bi-directional, there still an unmerge button
	Given author "3791" is merged with author "3794"
	And I sign in as an admin
	When I go to "/adminAuthor/update/id/3794"
	Then I should see "This author is merged with author Zhang G"
	And I should see "Unmerge"
