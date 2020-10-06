@admin-update-remove-publisher @issue-381
Feature: To remove publisher option in the admin
  As a curator,
  I do not want to see publisher option

  Background:
    Given Gigadb web site is loaded with production-like data


  @ok @issue-381
  Scenario: Guest user cannot visit admin dataset update page
    Given I am not logged in to Gigadb web site
    When I go to "/adminDataset/update/id/8"
    Then I should see "Login"

  @ok @issue-381
  Scenario: Admin user can visit admin dataset update page and cannot see publisher option
    Given I sign in as an admin
    When I am on "/adminDataset/update/id/8"
    And I should see "Update Dataset 100006"
    And I should see a button input "Save"
    Then I should not see "Publisher"