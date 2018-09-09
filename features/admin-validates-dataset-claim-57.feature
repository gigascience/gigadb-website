@issue-57 @admin-manages-dataset-claim @javascript @ok-docker
Feature: Upon notification of a dataset claim, an admin to validate the claim
	As a an admin
	I want to receive an notification when another gigadb user claim authorship on a dataset
	So that I can confirm or invalidate the claim

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
	And default admin user exists
	And user "joy_fox" is loaded

@ok
Scenario: On user edit form, admin sees a message with validate/reject button after user submit a claim
	Given a user has a pending claim for author "3791"
	And I sign in as an admin
	When I go to "/user/update/id/346"
	Then the response should contain "This user has a pending claim on author Zhang G"
	And the response should contain "Validate"
	And the response should contain "Reject"
	And the response should contain "Author info"


@ok
Scenario: When admin validates, user view is shown with a messaging indicating the user is linked to an author
	Given a user has a pending claim for author "3791"
	And I sign in as an admin
	And I am on "/user/update/id/346"
	When I follow "Validate"
	Then the response should contain "This user is linked to author: Zhang G (3791)"
	And the response should not contain "Validate"
	And the response should not contain "Reject"
	And the response should not contain "Author info"

@ok
Scenario: When admin rejects a claim, user edit form is shown with flash message
	Given a user has a pending claim for author "3791"
	And I sign in as an admin
	And I am on "/user/update/id/346"
	When I follow "Reject"
	Then the response should contain "Claimed rejected. No linking performed"


@ok
Scenario: On user view, when pending claim, admin sees a note about pending claim on author and a link to user edit form
	Given a user has a pending claim for author "3791"
	And I sign in as an admin
	When I go to "/user/view/id/346"
	Then the response should contain "This user has a pending claim"
	And the response should contain "Edit user to validate/reject the claim"

@ok
Scenario: On user view, when no pending claim, no linked author, no message is displayed
	Given I sign in as an admin
	When I go to "/user/view/id/344"
	Then the response should not contain "This user has a pending claim"
	And the response should not contain "Edit user to validate/reject the claim"
	And the response should not contain "This user is linked to author"

@ok
Scenario: From user view, when pending claim, admin can click on button to go to user edit form
	Given a user has a pending claim for author "3791"
	And I sign in as an admin
	And I am on "/user/view/id/346"
	When I follow "Edit user to validate/reject the claim"
	Then the response should contain "This user has a pending claim on author Zhang G"
	And the response should contain "Validate"
	And the response should contain "Reject"
	And the response should contain "Author info"
	And I should be on "/user/update/id/346"


@ok
Scenario: On author view, when pending claim, admin sees a note about pending claim on author and a link to user edit form
	Given a user has a pending claim for author "3791"
	And I sign in as an admin
	When I go to "/adminAuthor/view/id/3791"
	Then the response should contain "There is a pending claim on this author"
	And the response should contain "Edit user to validate/reject the claim"

@ok
Scenario: From author view, when pending claim, admin can click on button to go to user edit form
	Given a user has a pending claim for author "3791"
	And I sign in as an admin
	And I am on "/adminAuthor/view/id/3791"
	When I follow "Edit user to validate/reject the claim"
	Then the response should contain "This user has a pending claim on author Zhang G"
	And the response should contain "Validate"
	And the response should contain "Reject"
	And the response should contain "Author info"
	And I should be on "/user/update/id/346"


@ok
Scenario: On author view, when an author is linked to a user, a message shows the linked user name
	Given author "3791" is associated with a user
	And I sign in as an admin
	When I go to "/adminAuthor/view/id/3791"
	Then the response should contain "this author is linked to user Joy Fox (346)"
