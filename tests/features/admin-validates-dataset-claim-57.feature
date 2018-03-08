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
Scenario: Admin can access pending jobs from the administration page
	Given I sign in as an admin
	When I go to "/site/admin/"
	Then I should see "User Commands"

@ok
Scenario: Admin can see claims on dataset authorship
	Given a user has a pending claim for author "3791"
	And I sign in as an admin
	When I go to "/adminUserCommand/admin"
	And the response should contain "claim_author"
	Then the response should contain "Joy Fox (346)"
	And the response should contain "Zhang G (3791)"
	And the response should contain "pending"

@ok
Scenario: Admin can validate claims on dataset authorship
	Given a user has a pending claim for author "3791"
	And I sign in as an admin
	When I go to "/adminUserCommand/admin"
	And I follow "Validate claim"
	And I wait "2" seconds
	Then I should be on "/adminAuthor/view/id/3791"
	And I should see "346"

@ok
Scenario: Admin can invalidate claims on dataset authorship
	Given a user has a pending claim for author "3791"
	And I sign in as an admin
	When I go to "/adminUserCommand/admin"
	And I follow "Reject claim"
	Then the response should contain "No results found"