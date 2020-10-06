@admin-file @issue-457
  Feature: A curator can manage file attributes in admin file update page
    As a curator,
    I want to manage file attributes from the update form
    So that I can associate various attributes to files

  Background:
    Given Gigadb web site is loaded with production-like data


  @ok @issue-457
  Scenario: Guest user cannot visit admin file update page
    Given I am not logged in to Gigadb web site
    When I go to "/adminFile/update/"
    Then I should see "Login"

  @ok @issue-457
  Scenario: Sign in as admin and visit admin file update page and see New Attribute, Edit, Delete buttons
    Given I sign in as an admin
    When I am on "/adminFile/update/id/13973"
    Then I should see a button "New Attribute"
    And I should see "last_modified"
    And I should see "2013-7-15"
    And I should see a button input "Edit"
    And I should see a button input "Delete"

  @ok @issue-457 @javascript
  Scenario: Sign in as admin to delete attribute
    Given I sign in as an admin
    And I am on "/adminFile/update/id/13973"
    And I should see "last_modified"
    And I should see "2013-7-15"
    And I should see a button input "Delete"
    When I press "Delete"
    And I wait "3" seconds
    Then I should not see "last_modified"
    And I should not see "2013-7-15"
    And I should not see a button "Delete"




