@admin-file @issue-457
  Feature: An admin user can edit and delete attribute in admin file page
    As an admin user
    I can add/edit/save/delete attribute

  Background:
    Given Gigadb web site is loaded with production-like data
    When I go to "/adminFile/update/"
    Then I should see "Login"

  @ok @issue-457 @javascript
  Scenario: Sign in as admin and visit admin file update page
    Given I sign in as an admin
    When I go to "/adminFile/update/id/13973"
    Then I should see a button "New Attribute"

  @ok @issue-457 @javascript
  Scenario: Sign in as admin and see a Delete button
    Given I sign in as an admin
    When I go to "/adminFile/update/id/13973"
    Then I should see a button input "Delete"

  @ok @issue-457 @javascript
  Scenario: Sign in as admin and delete attribute
    Given I sign in as an admin
    When I go to "/adminFile/update/id/13973"
    And I take a screenshot named "before_delete"
    And I press "Delete"
    And I wait "3" seconds
    Then I take a screenshot named "after_delete"




