@issue-57 @user-claims-dataset @ok-docker
Feature: a user can claim his/her datasets
	As a gigadb user,
	I want to claim datasets that I've authored
	So I can manage them

Background:
	Given Gigadb web site is loaded with "gigadb_testdata.pgdmp" data
	And user "joy_fox" is loaded
	And default admin user exists

@ok
Scenario: Give users a button to claim a dataset they have authored
	Given I sign in as a user
	When I am on "/dataset/100002"
	Then I should see "Your dataset?"

@ok
Scenario: Non logged-in visitors should not see the button
	Given I am not logged in to Gigadb web site
	When I am on "/dataset/100002"
	Then I should not see "Your dataset?"


@ok @javascript
Scenario: a user is shown a modal to claim his/her dataset by reconcilling his/her author identity to his/her account
	Given I sign in as a user
	And dataset author "stephen_h_black" is loaded
	And I am on "/dataset/100002"
	When I follow "Your dataset?"
	# Then the response should contain "Select an author to link to your Gigadb User ID"
	And I wait "2" seconds
	Then I should see "David M Lambert"
	And I should see "Jun Wang"
	And I should see "Guojie Zhang"
	And I should see "Stephen Henry Black"
	And I should see "0000-0002-5486-853X"

@ok @javascript @insulate @tofix @todo
Scenario: a user select an author to claim and submit the claim form
	Given I sign in as a user
	And I am on "/dataset/100002"
	When I follow "Your dataset?"
	And I wait "1" seconds
	# And I take a screenshot named "expect_modal"
	And I click on button for author id "3791"
	And I wait "3" seconds
	Then the response should contain "Your claim has been submitted to the administrators."
	And the response should contain "You can close this box now."


@ok @javascript @claim-error-path
Scenario: a user with a pending claim visit dataset page and attempt to claim an author
	Given a user has a pending claim for author "3791"
	# Given a user has a "pending" claim for author "3791"
	And I sign in as a user
	And I am on "/dataset/100002"
	When I follow "Your dataset?"
	And I wait "1" seconds
	And I click on button for author id "3791"
	And I wait "1" seconds
	Then the response should contain "We cannot submit the claim:"
	And the response should contain "You already have a pending claim"
	And the response should contain "You can close this box now."


@ok @javascript @claim-error-path
Scenario: a user with a rejected claim visit dataset page and attempt to claim an author
	Given a user has a "rejected" claim for author "3791"
	And I sign in as a user
	And I am on "/dataset/100002"
	When I follow "Your dataset?"
	And I wait "1" seconds
	And I click on button for author id "3789"
	And I wait "3" seconds
	Then the response should contain "Your claim has been submitted to the administrators."
	And the response should contain "You can close this box now."

@ok @javascript @claim-error-path
Scenario: a user with a rejected claim visit dataset page and attempt to claim same author
	Given a user has a "rejected" claim for author "3791"
	And I sign in as a user
	And I am on "/dataset/100002"
	When I follow "Your dataset?"
	And I wait "2" seconds
	And I click on button for author id "3791"
	And I wait "2" seconds
	Then the response should contain "We cannot submit the claim:"
	And the response should contain "Your claim on this author has already been rejected"
	And the response should contain "You can close this box now."

@ok
Scenario: a user already associated to an author cannot claim another author
	Given author "3794" is associated with a user
	When I sign in as a user
    And I go to "/dataset/100002"
	Then I should not see "Your dataset?"

@ok @javascript
Scenario: a user with a pending claim can cancel the claim
	Given a user has a pending claim for author "3791"
	And I sign in as a user
	And I am on "/dataset/100002"
	When I follow "Your dataset?"
	And I wait "5" seconds
	And I follow "Cancel current claim"
	And I wait "5" seconds
	Then I should see "Your claim has been successfully canceled"
