@issue-57
Feature: Upon notification of a dataset claim, an admin to validate the claim
	As a an admin
	I want to receive an notification when another gigadb user claim authorship on a dataset
	So that I can confirm or invalidate the claim

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.sql" data
	And default admin user exists
	And user "joy_fox" is loaded

Scenario: Admin can see claims on dataset authorship
	Given I sign in as an admin
	And user "Joy Fox" has submitted a claim on author "Zhang G" of dataset "100002"
	When I visit "/adminCommand/admin"
	Then the response should contain "100"
	And the response should contain "Joy Fox"
	And the response should contain "Claim"
	And the response should contain "Zhang G"
	And the response should contain "100002"

Scenario: Admin can validate claims on dataset authorship
	Given I sign in as an admin
	And user "Joy Fox" has submitted a claim on author "Zhang G" of dataset "100002"
	When I visit "/adminCommand/admin"
	And I press "Confirm"
	Then I should be on "/adminAuthor/view/id/3791"
	And the response should contain "346"


Scenario: Admin can invalidate claims on dataset authorship
	Given I sign in as an admin
	And user "Joy Fox" has submitted a claim on author "Zhang G" of dataset "100002"
	When I visit "/adminCommand/admin"
	And I press "Invalidate"
	Then I should be on "/adminAuthor/view/id/3791"
	And the response should not contain "346"