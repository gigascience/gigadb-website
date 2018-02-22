@issue-57 @user-claims-dataset
Feature: a user can claim his/her datasets
	As a gigadb user,
	I want to claim datasets that I've authored
	So I can manage them

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.sql" data
	And user "joy_fox" is loaded

@wip
Scenario: Give users a button to claim a dataset they have authored
	Given I sign in as a user
	When I am on "/dataset/100002"
	Then I should see a "Are you an author of this dataset? claim your dataset" button


Scenario: Non logged-in visitors should not see the button
	Given I am not logged in to Gigadb web site
	When I am on "/dataset/100002"
	Then I should not see a "Are you an author of this dataset? claim your dataset" button



Scenario: a user can claim his/her dataset by reconcilling his/her author identity to his/her account
	Given I sign in as a user
	And I am on "/dataset/100002"
	When I press "Are you an author of this dataset? claim your dataset"
	Then the response should contain "Select your name"
	And the response should contain "Lambert, D, M"
	And the response should contain "Wang, J"
	And the response should contain "Zhang, G"
	And I should see a "Connect selected author to your identity" button



Scenario: a user reconcile his/her author identity with the user's gigadb account
	Given I sign in as a user
	And I have elected to reconcile author "Zhang, G" to my gigadb account
	When I press "Connect selected author to your identity"
	Then the response should contain "your claim is pending"
	And an email should be sent to the submitter of the dataset with a validation request
And an email should be sent to the curators with an approval request