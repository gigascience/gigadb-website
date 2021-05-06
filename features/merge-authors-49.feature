@issue-49 @merging-two-authors @javascript @ok-docker @timeout-prone
Feature: Merging duplicate authors
	In order to reduce data duplication and to increase datasets interlinking
	As an admin user
	I want to merge author records that are identical

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
	And default admin user exists
	And dataset "100002" exists

@ok
Scenario: On author edit form, there is a button to start the merging with another author
	Given I sign in as an admin
	When I go to "/adminAuthor/update/id/3791"
	Then I should see "Merge with an author"

@ok
Scenario: Presssing the merge an author button leads to author table and then merging of an author
	Given I sign in as an admin
	And I am on "/adminAuthor/update/id/3791"
	When I follow "Merge with an author"
	And I wait "2" seconds
	And I click on the row for author id "3794"
	And I wait "5" seconds
	And A dialog box reads "Confirm merging these two authors?"
	And I should see "ID:"
	And I should see "Surname:"
	And I should see "First name:"
	And I should see "Middle name:"
	And I should see "Orcid:"
	And I should see "3791"
	And I should see "3794"
	And I should see "Zhang"
	And I should see "Guojie"
	And I should see "Lambert"
	And I should see "David"
	And I should see "M"
	And I follow "Yes, merge authors"
	And I wait "2" seconds
	Then I should be on "/adminAuthor/view/id/3791"
	And I should see "merging authors completed successfully"

@ok
Scenario: Merging a new author already in a graph with another author
	Given I sign in as an admin
	And author "3791" is merged with author "3790"
	And author "3790" is merged with author "3789"
	And I am on "/adminAuthor/update/id/3791"
	When I follow "Merge with an author"
	And I wait "2" seconds
	And I click on the row for author id "3794"
	And I wait "1" seconds
	And A dialog box reads "Confirm merging these two authors?"
	And I should see "ID:"
	And I should see "Surname:"
	And I should see "First name:"
	And I should see "Middle name:"
	And I should see "Orcid:"
	And I should see "Already merged with:"
	And I should see "3791"
	And I should see "3794"
	And I should see "Zhang"
	And I should see "Guojie"
	And I should see "Lambert"
	And I should see "David"
	And I should see "M"
	And I should see "Lambert DM"
	And I should see "Wang J"
	And I follow "Yes, merge authors"
	And I wait "2" seconds
	Then I should be on "/adminAuthor/view/id/3791"
	And I should see "merging authors completed successfully"


@ok
Scenario: Merging a new author into a graph of identical authors
	Given I sign in as an admin
	And author "3792" is merged with author "3794"
	And author "3792" is merged with author "3789"
	And I am on "/adminAuthor/update/id/3791"
	When I follow "Merge with an author"
	And I wait "2" seconds
	And I click on the row for author id "3794"
	And I wait "1" seconds
	And A dialog box reads "Confirm merging these two authors?"
	And I should see "ID:"
	And I should see "Surname:"
	And I should see "First name:"
	And I should see "Middle name:"
	And I should see "Orcid:"
	And I should see "Already merged with:"
	And I should see "3791"
	And I should see "3794"
	And I should see "Zhang"
	And I should see "Guojie"
	And I should see "Pan"
	And I should see "S"
	And I should see "Lambert DM"
	And I should see "Cheng S"
	And I follow "Yes, merge authors"
	And I wait "2" seconds
	Then I should be on "/adminAuthor/view/id/3791"
	And I should see "merging authors completed successfully"

@ok
Scenario: Abort a merge from the popup confirmation box
	Given I sign in as an admin
	And I am on "/adminAuthor/update/id/3791"
	When I follow "Merge with an author"
	And I wait "2" seconds
	And I click on the row for author id "3794"
	And I wait "1" seconds
	And A dialog box reads "Confirm merging these two authors?"
	And I follow "No, abort and clear session"
	And I wait "1" seconds
	Then I should be on "/adminAuthor/view/id/3791"
	And I should not see "merging authors completed successfully"

@ok
Scenario: There is an unmerge button to disconnect two authors from an author edit form
	Given author "3791" is merged with author "3794"
	And I sign in as an admin
	When I go to "/adminAuthor/update/id/3791"
	Then I should see "this author is merged with author(s):"
	And I should see "Pan"
	And I should see "Unmerge author from those authors"

@ok
Scenario: No unmerge button appears when there is no author merged to the one being edited
	And I sign in as an admin
	When I go to "/adminAuthor/update/id/3791"
	Then I should not see "this author is merged with author(s):"
	And I should not see "Unmerge author from those authors"

@ok
Scenario: Cannot merge an author with himself
	Given I sign in as an admin
	And I am on "/adminAuthor/update/id/3791"
	When I follow "Merge with an author"
	And I wait "2" seconds
	And I click on the row for author id "3791"
	And I wait "1" seconds
	And A dialog box reads "Confirm merging these two authors?"
	And I follow "Yes, merge authors"
	Then I should see "Cannot merge with self. Choose another author to merge with"

@ok
Scenario: If exists (A1 identical_to A4), attempt to merge A1 with A4 again should not be possible
	Given author "3791" is merged with author "3794"
	And I sign in as an admin
	When I go to "/adminAuthor/update/id/3791"
	When I follow "Merge with an author"
	And I wait "2" seconds
	And I click on the row for author id "3794"
	And I wait "1" seconds
	And A dialog box reads "Confirm merging these two authors?"
	And I follow "Yes, merge authors"
	Then I should see "Authors already merged. Choose another author to merge with"

@ok
Scenario: If exists (A1 identical_to A4), attempt to merge A4 with A1 should not be possible
	Given author "3791" is merged with author "3794"
	And I sign in as an admin
	When I go to "/adminAuthor/update/id/3794"
	And I follow "Merge with an author"
	And I wait "2" seconds
	And I click on the row for author id "3791"
	And I wait "1" seconds
	And A dialog box reads "Confirm merging these two authors?"
	And I follow "Yes, merge authors"
	Then I should see "Authors already merged. Choose another author to merge with"

@ok
Scenario: If exists (A1 identical_to A4), A4 view shows link to A1
	Given author "3791" is merged with author "3794"
	And I sign in as an admin
	When I go to "/adminAuthor/view/id/3794"
	Then I should see "this author is merged with author(s):"
	Then I should see "3791. Guojie Zhang (Orcid: n/a)"

@ok
Scenario: If exists (A1 i_t A2), (A1 i_t A3) and (A2 i_t A5), on A1 view: a graph of merged authors is shown properly
	Given author "3791" is merged with author "3792"
	And author "3791" is merged with author "3793"
	And author "3792" is merged with author "3795"
	And I sign in as an admin
	When I go to "/adminAuthor/view/id/3791"
	Then I should see "this author is merged with author(s):"
	Then I should see "3792"
	Then I should see "3793"
	Then I should see "3795"

@ok
Scenario: If exists (A1 i_t A2), (A1 i_t A3) and (A2 i_t A5), a graph of merged authors is shown properly on A5
	Given author "3791" is merged with author "3792"
	And author "3791" is merged with author "3793"
	And author "3792" is merged with author "3795"
	And I sign in as an admin
	When I go to "/adminAuthor/view/id/3795"
	Then I should see "this author is merged with author(s):"
	Then I should see "3791"
	Then I should see "3792"
	Then I should see "3793"

@ok
Scenario: If exists (A1 i_t A2), (A1 i_t A3) and (A2 i_t A5), on A1 edit form: shows links and an unmerge button
	Given author "3791" is merged with author "3792"
	And author "3791" is merged with author "3793"
	And author "3792" is merged with author "3795"
	And I sign in as an admin
	When I go to "/adminAuthor/update/id/3791"
	Then I should see "this author is merged with author(s):"
	And I should see "3792"
	And I should see "3793"
	And I should see "3795"
	And I should see "Unmerge author from those authors"

@ok
Scenario: If exists (A1 i_t A2), (A1 i_t A3) and (A2 i_t A5), on A5 edit form: shows links and an unmerge button
	Given author "3791" is merged with author "3792"
	And author "3791" is merged with author "3793"
	And author "3792" is merged with author "3795"
	And I sign in as an admin
	When I go to "/adminAuthor/update/id/3795"
	Then I should see "this author is merged with author(s):"
	And I should see "3791"
	And I should see "3792"
	And I should see "3793"
	And I should see "Unmerge author from those authors"

@ok
Scenario: If exists (A1 i_t A2), (A1 i_t A3) and (A2 i_t A5), on A3 edit form, pressing unmerge removes A3 from graph
	Given author "3791" is merged with author "3792"
	And author "3791" is merged with author "3793"
	And author "3792" is merged with author "3795"
	And I sign in as an admin
	When I go to "/adminAuthor/update/id/3793"
	And I follow "Unmerge author from those authors"
	And I wait "2" seconds
	Then I should be on "/adminAuthor/view/id/3793"
	And I should see "author unmerged from other authors"
	And I should not see "3791"
	And I should not see "3792"
	And I should not see "3795"
	And I should not see "Unmerge author from those authors"


@ok
Scenario: On user profile, show the datasets ( 100002 and 100003) of linked author and of authors merged to the linked author
	Given author "3791" is merged with author "3792"
	And user "joy_fox" is loaded
	And I sign in as a user
	And I am linked to author "Zhang, G"
	When I am on "/user/view_profile"
	And I follow "Your Authored Datasets"
	Then I should see "Genomic data from Adelie penguin (Pygoscelis adeliae)"
	And I should see "Genome data from foxtail millet (Setaria italica)"

@ok
Scenario: cannot go through the workflow for linking author to user and for merging two authors at the same time (1)
	Given I sign in as an admin
	And I have initiated the search of an author for Gigadb User with ID "345"
	When I go to "/adminAuthor/update/id/3794"
	And I follow "Merge with an author"
	Then I should see "Click on a row to proceed with merging that author with"
	And I should not see "Click on a row to proceed with linking that author with user"


@ok
Scenario: cannot go through the workflow for linking author to user and for merging two authors at the same time (2)
	Given I sign in as an admin
	And I am on "/adminAuthor/update/id/3794"
	And I follow "Merge with an author"
	When I go to "/user/update/id/345"
	And I follow "Link this user to an author"
	Then I should not see "Click on a row to proceed with merging that author with"
	And I should see "Click on a row to proceed with linking that author with user"

