@issue-57 @admin-manages-dataset-claim @javascript
Feature: Upon notification of a dataset claim, an admin to validate the claim
	As a an admin
	I want to receive an notification when another gigadb user claim authorship on a dataset
	So that I can confirm or invalidate the claim

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.sql" data
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

